<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Models\BuilderContextInterface;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\CollectionPaginator;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\RenderContext;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Services\FileManagerInterface;
use Terdelyi\Phanstatic\Support\OutputInterface;

class CollectionBuilder implements BuilderInterface
{
    private string $sourcePath = 'collections';
    private Config $config;
    private OutputInterface $output;
    private FileManagerInterface $fileManager;

    public function __construct(BuilderContextInterface $context)
    {
        $this->config = $context->getConfig();
        $this->output = $context->getOutput();
        $this->fileManager = $context->getFileManager();
    }

    /**
     * @throws \Throwable
     */
    public function build(): void
    {
        if (!$this->fileManager->exists($this->getSourcePath())) {
            $this->output->action("Skipping collections: no 'content/collections' directory found");
            $this->output->space();

            return;
        }

        $this->output->action('Looking for collections...');

        foreach ($this->getCollections() as $directory) {
            $collection = $this->parseCollection($directory);

            if (!$this->fileManager->exists($collection->singleTemplate)) {
                throw new \Exception('Collection must have a single template exist at '.$collection->singleTemplate);
            }

            $this->output->writeln('<fg=yellow>Collection '.ucfirst($collection->basename).' found. Looking for items...</>');

            $collectionFiles = $this->getFilesByCollection($collection->sourceDir);

            if ($collectionFiles->count() === 0) {
                $this->output->warning('No items available in this collection.');

                continue;
            }

            foreach ($collectionFiles as $file) {
                $output = $this->processPage($file, $collection);
                $this->output->action($output);
            }

            if (!$this->fileManager->exists($collection->indexTemplate)) {
                continue;
            }

            $output = $this->generateIndexPages($collection);

            foreach ($output as $line) {
                $this->output->action($line);
            }
        }

        $this->output->space();
    }

    private function getCollections(): Finder
    {
        return $this->fileManager->getDirectories($this->getSourcePath(), '== 0');
    }

    private function getFilesByCollection(string $directory): Finder
    {
        return $this->fileManager->getFiles($directory, '*.md');
    }

    /**
     * @throws CommonMarkException
     * @throws \Exception
     */
    private function processPage(SplFileInfo $file, Collection $collection): string
    {
        $page = $this->loadPage($file, $collection->slug);
        $context = $this->makeRenderContext($page, $collection);
        $fileContent = $this->fileManager->render($collection->singleTemplate, $context);

        $savedFile = $this->fileManager->save($page->path, $fileContent);

        if (!$savedFile) {
            throw new \Exception('Failed to save file: '.$page->path);
        }

        $collectionItem = $this->makeCollectionItem(
            $page->title ?? '',
            $page->url ?? '',
            $page->description ?? '',
            $page->date ?? date(\DateTimeInterface::ATOM, $file->getMTime())
        );
        $collection->add($collectionItem);

        $outputFrom = $this->getSourcePath($file->getRelativePathname(), true);
        $outputTo = $this->getDestinationPath($page->relativePath, true);

        return $outputFrom.' => '.$outputTo;
    }

    /**
     * @throws CommonMarkException
     * @throws \Exception
     */
    private function loadPage(SplFileInfo $file, string $collectionSlug): Page
    {
        $page = $this->buildPage($file->getBasename('.md'), $collectionSlug);
        $fileContent = file_get_contents($file->getPathname());

        if (!$fileContent) {
            throw new \Exception('File is empty: '.$file->getPathname());
        }

        $parsedFile = YamlFrontMatter::parse($fileContent);
        $meta = $parsedFile->matter();

        if (!isset($meta['date'])) {
            $meta['date'] = date(\DateTimeInterface::ATOM, $file->getMTime());
        }

        $title = $parsedFile->matter('title');
        $body = (new CommonMarkConverter())->convert($parsedFile->body());
        unset($meta['title']);

        $page->title = $title;
        $page->content = $body->getContent();
        $page->meta = $meta;

        return $page;
    }

    private function buildPage(string $basename, string $collectionSlug): Page
    {
        $permalink = "/{$collectionSlug}/{$basename}/";

        if ($basename === 'index') {
            $permalink = '/';
        }

        $permalinkWithoutSlash = substr($permalink, 1);

        $newPath = $this->getDestinationPath($permalinkWithoutSlash.'index.html');

        if ($permalink === '/') {
            $newPath = $this->getDestinationPath('index.html');
        }

        $relativePath = $permalink === '/' ? 'index.html' : $permalinkWithoutSlash.'index.html';

        return $this->makePage(
            $newPath,
            $relativePath,
            $permalink,
            url($permalink)
        );
    }

    private function parseCollection(SplFileInfo $directory): Collection
    {
        $collectionConfig = $this->config->getCollections($directory->getBasename());

        return new Collection(
            title: $collectionConfig->title ?? $directory->getBasename(),
            basename: $directory->getBasename(),
            sourceDir: $directory->getPathname(),
            slug: $collectionConfig->slug ?? $directory->getBasename(),
            singleTemplate: $directory->getPathname().'/single.php',
            indexTemplate: $directory->getPathname().'/index.php',
            items: [],
            pageSize: $collectionConfig->pageSize ?? 10
        );
    }

    /**
     * @return string[]
     *
     * @throws \Throwable
     */
    private function generateIndexPages(Collection $collection): array
    {
        $itemsTotal = $collection->count();
        $pagesRequired = (int) ceil($itemsTotal / $collection->pageSize);
        $output = [];

        for ($page = 1; $page <= $pagesRequired; ++$page) {
            $pagination = CollectionPaginator::create($page, $pagesRequired, $collection->slug, $itemsTotal);
            $indexSlug = $this->getIndexSlug($collection, $page);
            $targetFile = $this->getDestinationPath($indexSlug.'/index.html');
            $current = $this->getCurrent($collection, $page);
            $pageData = $this->makePage($targetFile, $indexSlug.'/index.html', $current, url($current));
            $data = $this->getRenderContext($collection, $page, $pageData, $pagination, $pagesRequired);

            $html = $this->fileManager->render($collection->indexTemplate, $data);
            $savedFile = $this->fileManager->save($targetFile, $html);

            if (!$savedFile) {
                throw new \Exception('Failed to save file: '.$targetFile);
            }

            $outputFrom = $this->getSourcePath($collection->slug, true);
            $outputTo = $this->getDestinationPath($indexSlug.'/index.html', true);
            $output[] = 'Index page: '.$outputFrom.' => '.$outputTo;
        }

        return $output;
    }

    private function getIndexSlug(Collection $collection, int $page): string
    {
        return $page > 1 ? $collection->slug.'/page/'.$page : $collection->slug;
    }

    private function getCurrent(Collection $collection, int $page): string
    {
        return $page != 1 ? $collection->slug.'/page/'.$page : '/'.$collection->slug.'/';
    }

    private function getRenderContext(Collection $collection, int $page, Page $pageData, CollectionPaginator $paginator, int $pagesRequired): RenderContext
    {
        $items = $collection->items();

        usort($items, function (CollectionItem $a, CollectionItem $b) {
            return $b->date <=> $a->date;
        });

        $collection = $collection->setItems($items)->slice(($page - 1) * $collection->pageSize, $collection->pageSize);
        $pagination = $pagesRequired > 1 ? $paginator : null;

        return $this->makeRenderContext($pageData, $collection, $pagination);
    }

    private function makeCollectionItem(string $title, string $link, string $excerpt, string $date): CollectionItem
    {
        return new CollectionItem(
            title: $title,
            link: $link,
            excerpt: $excerpt,
            date: $date
        );
    }

    /**
     * @param array<string,mixed> $meta
     */
    private function makePage(string $targetFile, string $relativeTargetFile, string $current, string $url, ?string $title = null, ?string $content = null, array $meta = []): Page
    {
        return new Page(
            path: $targetFile,
            relativePath: $relativeTargetFile,
            permalink: $current,
            url: $url,
            title: $title,
            content: $content,
            meta: $meta
        );
    }

    private function makeRenderContext(Page $page, Collection $collection, ?CollectionPaginator $pagination = null): RenderContext
    {
        $site = new Site(
            title: $this->config->getTitle(),
            baseUrl: $this->config->getBaseUrl(),
            meta: $this->config->getMeta()
        );

        return new RenderContext(
            site: $site,
            page: $page,
            collection: $collection,
            pagination: $pagination,
        );
    }

    private function getSourcePath(?string $path = null, bool $relative = false): string
    {
        $sourcePath = $path !== null ? $this->sourcePath.'/'.$path : $this->sourcePath;

        return $this->config->getSourceDir($sourcePath, $relative);
    }

    private function getDestinationPath(?string $path = null, bool $relative = false): string
    {
        return $this->config->getBuildDir($path, $relative);
    }
}
