<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators\Collection;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Compilers\MarkdownCompiler;
use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Generators\GeneratorInterface;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class CollectionGenerator implements GeneratorInterface
{
    use OutputHelper;

    private string $sourcePath = 'collections';
    private Filesystem $filesystem;
    private Helpers $helpers;
    private Config $config;
    private MarkdownCompiler $markdownCompiler;
    private PhpCompiler $phpCompiler;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->helpers = new Helpers();
        $this->config = Config::get();
        $this->markdownCompiler = new MarkdownCompiler();
        $this->phpCompiler = new PhpCompiler();
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->setOutput($output);

        $this->text('Looking for collections...');
        $this->lines();

        if ( ! $this->filesystem->exists($this->getCollectionsDir())) {
            $this->text('Skipping pages: %s directory doesn\'t exist', $this->getCollectionsDir());

            return;
        }

        $collections = (new FileReader())->findDirectories($this->getCollectionsDir());
        foreach ($collections as $key => $collection) {
            $this->process($collection);

            // @TODO: Conditions should be supported by the OutputHelper
            if ((int) $key + 1 > count($collections)) {
                $this->lines();
            }
        }
    }

    private function process(SplFileInfo $directory): void
    {
        $collection = $this->parseCollection($directory);
        $this->text("Collection '%s' found. Looking for items...", $collection->title);

        $files = (new FileReader())->findFiles($collection->sourceDir, '*.md');

        if ($files->count() === 0) {
            $this->text('No items available in this collection.');

            return;
        }

        foreach ($files as $file) {
            $this->processPage($file, $collection);
        }

        $this->processIndexPages($collection);

        $this->lines();
    }

    /**
     * @throws \Exception
     */
    private function parseCollection(SplFileInfo $directory): Collection
    {
        // @TODO: Simplify this if Collection in config is set
        $collection = $directory->getBasename();
        $config = $this->config->collections[$collection] ?? null;
        $title = $config->title ?? ucwords($directory->getBasename());

        if ( ! $config) {
            throw new \Exception(sprintf("Configuration for collection '%s' is missing", $title));
        }

        $singleTemplate = $directory->getPathname().'/single.php';
        $indexTemplate = $directory->getPathname().'/index.php';

        if ( ! $this->filesystem->exists($singleTemplate)) {
            throw new \Exception(sprintf("Collection '%s' must have a single template at %s", $title, $singleTemplate));
        }

        return new Collection(
            title: $title,
            basename: $directory->getBasename(),
            sourceDir: $directory->getPathname(),
            slug: ! empty($config->slug) ? $config->slug : $directory->getBasename(),
            singleTemplate: $singleTemplate,
            indexTemplate: $indexTemplate,
            items: [],
            pageSize: $config->pageSize ?? 10
        );
    }

    private function processPage(SplFileInfo $file, Collection $collection): void
    {
        $context = (new SingleContextBuilder())->build($file, $collection);
        $context->collection = $collection;

        $html = $this->phpCompiler->render($collection->singleTemplate, $context);

        try {
            $this->filesystem->dumpFile($context->page->path, $html);
        } catch (IOException $ex) {
            throw new \Exception('Failed to save file: '.$context->page->path.' '.$ex->getMessage());
        }

        $this->addPageToCollection($collection, $context->page);

        $this->fromTo(
            $this->helpers->getSourceDir($file->getRelativePathname(), true),
            $this->helpers->getBuildDir($context->page->relativePath, true)
        );
    }

    private function addPageToCollection(Collection $collection, Page $page): void
    {
        $collectionItem = CollectionItem::fromPage($page);
        $collection->add($collectionItem);
    }

    private function processIndexPages(Collection $collection): void
    {
        $totalItems = $collection->count();
        $totalPages = (int) ceil($totalItems / $collection->pageSize);

        for ($page = 1; $page <= $totalPages; ++$page) {
            $context = (new IndexContextBuilder($collection, $page))->build();
            $html = (new PhpCompiler())->render($collection->indexTemplate, $context);

            try {
                $this->filesystem->dumpFile($context->page->path, $html);
            } catch (IOException $ex) {
                throw new \Exception('Failed to save file: '.$context->page->path.' '.$ex->getMessage());
            }

            $this->text('Paginated page created (%s/%s): %s', $page, $totalPages, $context->page->relativePath);
        }
    }

    private function getCollectionsDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }
}
