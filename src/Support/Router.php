<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Generators\Collection\IndexContextBuilder;
use Terdelyi\Phanstatic\Generators\Collection\SingleContextBuilder;
use Terdelyi\Phanstatic\Generators\Page\ContextBuilder;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Readers\FileReader;

class Router
{
    private string $uri;

    private Helpers $helpers;

    public function __construct(?Helpers $helpers = null)
    {
        $this->helpers = $helpers ?? new Helpers();
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $request): void
    {
        $this->uri = $this->parseRequest($request);

        if ($page = $this->getPage()) {
            $file = File::fromPath($page, ['pages']);
            $context = (new ContextBuilder())->build($file);
            $this->render($file->getPathname(), $context);
        }

        if ($collection = $this->getCollection()) {
            if ($collection->collection && $collection->page->type === Page::TYPE_COLLECTION_SINGLE) {
                $this->render($collection->collection->singleTemplate, $collection);
            } elseif ($collection->collection) {
                $this->render($collection->collection->indexTemplate, $collection);
            }
        }
    }

    public function parseRequest(string $request): string
    {
        $path = (string) parse_url($request, PHP_URL_PATH);
        $uri = trim($path, '/');

        return $uri === ''
            ? 'index'
            : htmlspecialchars($uri);
    }

    public function getPage(): ?string
    {
        $file = $this->helpers->getSourceDir('pages/'.$this->uri.'.php');

        return file_exists($file) ? $file : null;
    }

    public function getCollection(): ?CompilerContext
    {
        $matchedCollectionConfig = null;

        foreach (Config::get()->collections as $key => $collection) {
            if (str_starts_with($this->uri, (string) $collection->slug) || str_starts_with($this->uri, $key)) {
                $matchedCollectionConfig = $collection;

                break;
            }
        }

        if ( ! $matchedCollectionConfig) {
            return null;
        }

        $path = 'collections/'.$matchedCollectionConfig->slug;
        $directory = File::fromPath($this->helpers->getSourceDir($path));
        $singleTemplate = $directory->getPathname().'/single.php';
        $indexTemplate = $directory->getPathname().'/index.php';

        $collection = new Collection(
            title: $matchedCollectionConfig->title ?? '',
            basename: $directory->getBasename(),
            sourceDir: $directory->getPathname(),
            slug: ! empty($matchedCollectionConfig->slug) ? $matchedCollectionConfig->slug : $directory->getBasename(),
            singleTemplate: $singleTemplate,
            indexTemplate: $indexTemplate,
            items: [],
            pageSize: $matchedCollectionConfig->pageSize ?? 10
        );

        $files = (new FileReader())->findFiles($this->helpers->getSourceDir($path), '*.md');
        $this->applyItems($files, $collection);

        $single = null;
        foreach ($files as $file) {
            if ($this->uri === $matchedCollectionConfig->slug.'/'.$file->getFilenameWithoutExtension()) {
                $single = $file;
            }
        }

        if ($single) {
            $context = (new SingleContextBuilder())->build($single, $collection);
            $context->collection = $collection;

            return $context;
        }

        $permalinkParts = explode('/', $this->uri);
        $permalinkParts = array_reverse($permalinkParts);
        $page = (int) $permalinkParts[0] ?: 1;

        return (new IndexContextBuilder($collection, $page))->build();
    }

    /**
     * @throws \Throwable
     */
    private function render(string $file, CompilerContext $context): void
    {
        echo (new PhpCompiler())->render($file, $context);

        exit;
    }

    private function applyItems(Finder $files, Collection $collection): void
    {
        foreach ($files as $file) {
            $context = (new SingleContextBuilder())->build($file, $collection);
            $collectionItem = CollectionItem::fromPage($context->page);
            $collection->add($collectionItem);
        }
    }
}
