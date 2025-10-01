<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Compilers\MarkdownCompiler;
use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\CollectionPaginator;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Phanstatic;
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
        $this->helpers = Phanstatic::get()->helpers;
        $this->config = Phanstatic::get()->config;
        $this->markdownCompiler = new MarkdownCompiler();
        $this->phpCompiler = new PhpCompiler();
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->setOutput($output);

        $this->text('Looking for collections...');

        if ( ! $this->filesystem->exists($this->getCollectionsDir())) {
            $this->text('Skipping pages: %s directory doesn\'t exist', $this->getCollectionsDir());

            return;
        }

        $collections = (new FileReader())->findDirectories($this->getCollectionsDir());
        foreach ($collections as $collection) {
            $this->process($collection);
        }

        $this->lines();
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
    }

    /**
     * @throws \Exception
     */
    private function parseCollection(SplFileInfo $directory): Collection
    {
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
            slug: $config->slug ?? $directory->getBasename(),
            singleTemplate: $singleTemplate,
            indexTemplate: $indexTemplate,
            items: [],
            pageSize: $config->pageSize ?? 10
        );
    }

    private function processPage(SplFileInfo $file, Collection $collection): void
    {
        $markdown = $this->markdownCompiler->render($file->getPathname());
        $page = $this->buildPage($file->getBasename('.md'), $collection->slug, $markdown);
        $context = $this->buildContext($page, $collection);
        $html = $this->phpCompiler->render($collection->singleTemplate, $context);

        try {
            $this->filesystem->dumpFile($page->path, $html);
        } catch (IOException $ex) {
            throw new \Exception('Failed to save file: '.$page->path.' '.$ex->getMessage());
        }

        $this->addPageToCollection($collection, $page);

        $this->fromTo(
            $this->helpers->getSourceDir($file->getRelativePathname(), true),
            $this->helpers->getBuildDir($page->relativePath, true)
        );
    }

    private function buildPage(string $basename, string $collectionSlug, MarkdownCompiler $markdown): Page
    {
        $permalink = $collectionSlug !== '' ? "/{$collectionSlug}/{$basename}/" : "/{$basename}/";

        if ($basename === 'index') {
            $permalink = '/';
        }

        $permalinkWithoutSlash = substr($permalink, 1);

        $newPath = $this->helpers->getBuildDir($permalinkWithoutSlash.'index.html');

        if ($permalink === '/') {
            $newPath = $this->helpers->getBuildDir('index.html');
        }

        $relativePath = $permalink === '/' ? 'index.html' : $permalinkWithoutSlash.'index.html';

        $meta = $markdown->meta();
        $title = ! isset($meta['title']) ? dd($markdown) : $meta['title'];
        unset($meta['title']);

        return new Page(
            path: $newPath,
            relativePath: $relativePath,
            permalink: $permalink,
            url: url($permalink),
            title: $title,
            content: $markdown->content(),
            meta: $meta,
        );
    }

    private function processIndexPages(Collection $collection): void {}

    private function buildContext(Page $page, Collection $collection, ?CollectionPaginator $pagination = null): CompilerContext
    {
        $site = new Site(
            title: $this->config->title,
            baseUrl: $this->config->baseUrl,
            meta: $this->config->meta,
        );

        return new CompilerContext(
            site: $site,
            page: $page,
            collection: $collection,
            pagination: $pagination,
        );
    }

    private function addPageToCollection(Collection $collection, Page $page): void
    {
        // @TODO: Date could be part of page - filemtime or meta
        $date = $page->meta['date'];
        unset($page->meta['date']);

        $collectionItem = new CollectionItem(
            title: $page->title ?? '',
            link: $page->url ?? '',
            excerpt: $page->description ?? '',
            date: $date,
            meta: $page->meta,
        );

        $collection->add($collectionItem);
    }

    private function getCollectionsDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }
}
