<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Generators\Collection\IndexContextBuilder;
use Terdelyi\Phanstatic\Generators\Collection\SingleContextBuilder;
use Terdelyi\Phanstatic\Generators\Page\ContextBuilder;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Readers\FileReader;

class Router
{
    private string $uri;

    private Helpers $helpers;

    public function __construct(?Helpers $helpers = null)
    {
        $this->helpers = $helpers ?? new Helpers();
    }

    public function handle(string $request): void
    {
        $this->uri = $this->parseRequest($request);

        if ($page = $this->getPage()) {
            $file = File::fromPath($page);
            $context = (new ContextBuilder())->build($file);
            $this->render($file->getPathname(), $context);
        }

        if ($collection = $this->getCollection()) {
            $this->render($file->getPathname(), $collection);
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

        $single = null;
        $path = 'collections/'.$matchedCollectionConfig->slug;
        $files = (new FileReader())->findFiles($this->helpers->getSourceDir($path), '*.md');
        foreach ($files as $file) {
            if ($this->uri === $matchedCollectionConfig->slug.'/'.$file->getFilenameWithoutExtension()) {
                $single = $file;
            }
        }

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

        $this->getItems($this->helpers->getSourceDir($path), $collection);

        if ($single) {
            $context = (new SingleContextBuilder())->build($single, $collection);
            $this->render($collection->singleTemplate, $context);
        }

        $page = 1;
        $context = (new IndexContextBuilder($collection, $page))->build();
        $this->render($collection->indexTemplate, $context);
    }

    /**
     * @throws \Throwable
     */
    private function render(string $file, CompilerContext $context): void
    {
        echo (new PhpCompiler())->render($file, $context);

        exit;
    }

    private function getItems(string $path, Collection $collection): void
    {
        $files = (new FileReader())->findFiles($path, '*.md');
        $items = [];

        foreach ($files as $file) {
            $context = (new SingleContextBuilder())->build($file, $collection);
            $collectionItem = CollectionItem::fromPage($context->page);
            $collection->add($collectionItem);
        }
    }
}
