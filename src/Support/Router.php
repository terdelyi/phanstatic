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
        $this->parseRequest($request);

        if ($page = $this->parsePage()) {
            $file = SplFileInfo::fromFilePath($page, 'pages');
            $context = (new ContextBuilder())->build($file);

            echo $this->render($file->getPathname(), $context);

            exit;
        }

        if ($collection = $this->parseCollection()) {
            if ( ! $collection->collection) {
                return;
            }

            $template = $collection->page->type === Page::TYPE_COLLECTION_SINGLE
                ? $collection->collection->singleTemplate
                : $collection->collection->indexTemplate;

            echo $this->render($template, $collection);

            exit;
        }
    }

    public function parseRequest(string $request): void
    {
        $path = (string) parse_url($request, PHP_URL_PATH);
        $uri = trim($path, '/');

        $this->uri = $uri === ''
            ? 'index'
            : htmlspecialchars($uri);
    }

    public function parsePage(): ?string
    {
        $fileName = 'pages/'.$this->uri.'.php';
        $filePath = $this->helpers->getSourceDir($fileName);

        return file_exists($filePath) ? $filePath : null;
    }

    public function parseCollection(): ?CompilerContext
    {
        foreach (Config::get()->collections as $key => $collection) {
            $matchesSlug = str_starts_with($this->uri, (string) $collection->slug);
            $matchesKey = str_starts_with($this->uri, $key);

            if ( ! $matchesSlug && ! $matchesKey) {
                continue;
            }

            $matchedCollectionConfig = $collection;
        }

        if (empty($matchedCollectionConfig)) {
            return null;
        }

        $collectionDir = 'collections/'.$matchedCollectionConfig->slug;
        $collectionDirPath = $this->helpers->getSourceDir($collectionDir);
        $directory = SplFileInfo::fromFilePath($collectionDirPath, 'collections');
        $slug = ! empty($matchedCollectionConfig->slug)
            ? $matchedCollectionConfig->slug
            : $directory->getBasename();

        $collection = new Collection(
            title: $matchedCollectionConfig->title ?? '',
            basename: $directory->getBasename(),
            sourceDir: $directory->getPathname(),
            slug: $slug,
            singleTemplate: $directory->getPathname().'/single.php',
            indexTemplate: $directory->getPathname().'/index.php',
            items: [],
            pageSize: $matchedCollectionConfig->pageSize
        );

        $itemsPath = $this->helpers->getSourceDir($collectionDir);
        $items = (new FileReader())->findFiles($itemsPath, '*.md');

        $this->applyItems($items, $collection);

        foreach ($items as $file) {
            $permalink = $matchedCollectionConfig->slug.'/'.$file->getFilenameWithoutExtension();
            if ($this->uri === $permalink) {
                $context = (new SingleContextBuilder())->build($file, $collection);
                $context->collection = $collection;

                return $context;
            }
        }

        $uriParts = explode('/', $this->uri);
        $uriParts = array_reverse($uriParts);
        $page = (int) $uriParts[0] ?: 1;

        return (new IndexContextBuilder($collection, $page))->build();
    }

    private function applyItems(Finder $files, Collection $collection): void
    {
        foreach ($files as $file) {
            $context = (new SingleContextBuilder())->build($file, $collection);
            $collectionItem = CollectionItem::fromPage($context->page);
            $collection->add($collectionItem);
        }
    }

    /**
     * @throws \Throwable
     */
    private function render(string $file, CompilerContext $context): string
    {
        return (new PhpCompiler())->render($file, $context);
    }
}
