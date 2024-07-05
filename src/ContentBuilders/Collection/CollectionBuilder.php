<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders\Collection;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\SiteConfig;
use Terdelyi\Phanstatic\ContentBuilders\BuilderContextInterface;
use Terdelyi\Phanstatic\ContentBuilders\BuilderInterface;
use Terdelyi\Phanstatic\ContentBuilders\Page\Page;
use Terdelyi\Phanstatic\ContentBuilders\RenderContext;
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

            return;
        }

        $this->output->action('Looking for collections...');

        $collectionDirectories = $this->fileManager->getDirectories($this->getSourcePath(), '== 0');

        foreach ($collectionDirectories as $directory) {
            $collection = $this->createCollection($directory);

            if (!$this->fileManager->exists($collection->singleTemplate)) {
                throw new \Exception('Collection must have a single template exist at '.$collection->singleTemplate);
            }

            $this->output->writeln('<fg=yellow>'.ucfirst($collection->basename).' collection found. Looking for items...</>');

            $collectionContent = $this->fileManager->getFiles($collection->sourceDir, '*.md');

            if ($collectionContent->count() === 0) {
                $this->output->warning('No items available in this collection.');

                continue;
            }

            foreach ($collectionContent as $file) {
                [$page, $data] = $this->buildPages($file, $collection);

                $item = new CollectionItem(
                    title: $page->title ?? '',
                    link: $page->url ?? '',
                    excerpt: $data->page->description ?? '',
                    date: $data->page->date ?? date(\DateTimeInterface::ATOM, $file->getMTime())
                );

                $collection->add($item);
            }

            if ($this->fileManager->exists($collection->indexTemplate)) {
                $this->buildIndexPages($collection);
            }
        }

        $this->output->space();
    }

    public function getSite(): SiteConfig
    {
        return new SiteConfig(
            title: $this->config->getTitle(),
            baseUrl: $this->config->getBaseUrl(),
            meta: $this->config->getMeta(),
        );
    }

    /**
     * @throws CommonMarkException
     * @throws \Exception
     */
    private function getPage(SplFileInfo $file, string $collectionSlug): Page
    {
        $fileData = $this->getFileData($file->getBasename('.md'), $collectionSlug);
        $fileContent = file_get_contents($file->getPathname());

        if (!$fileContent) {
            throw new \Exception('File is empty: '.$file->getPathname());
        }

        $parsedFile = YamlFrontMatter::parse($fileContent);
        $meta = $parsedFile->matter();
        $title = $parsedFile->matter('title');
        $body = (new CommonMarkConverter())->convert($parsedFile->body());
        unset($meta['title']);

        return new Page(
            path: $fileData['path'],
            relativePath: $fileData['relativePath'],
            permalink: $fileData['permalink'],
            url: url($fileData['permalink']),
            title: $title,
            content: $body->getContent(),
            meta: $meta
        );
    }

    /**
     * @return array<string,string>
     */
    private function getFileData(string $basename, string $collectionSlug): array
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

        return [
            'path' => $newPath,
            'relativePath' => $relativePath,
            'permalink' => $permalink,
        ];
    }

    private function createCollection(SplFileInfo $directory): Collection
    {
        $collectionConfig = $this->config->getCollections($directory->getBasename());

        return new Collection(
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
     * @return array{Page, RenderContext}
     *
     * @throws CommonMarkException
     * @throws \Exception|\Throwable
     */
    private function buildPages(SplFileInfo $file, Collection $collection): array
    {
        $page = $this->getPage($file, $collection->slug);
        $data = new RenderContext(
            site: $this->getSite(),
            page: $page,
        );

        if ($this->fileManager->save($page->path, $this->fileManager->render($collection->singleTemplate, $data)) !== false) {
            $outputFrom = $this->getSourcePath($file->getRelativePathname(), true);
            $outputTo = $this->getDestinationPath($page->relativePath, true);
            $this->output->file($outputFrom.' => '.$outputTo);
        }

        return [$page, $data];
    }

    /**
     * @throws \Throwable
     */
    private function buildIndexPages(Collection $collection): void
    {
        $itemsTotal = $collection->count();
        $pagesRequired = (int) ceil($itemsTotal / $collection->pageSize);

        for ($page = 1; $page <= $pagesRequired; ++$page) {
            $pagination = CollectionPaginator::create($page, $pagesRequired, $collection->slug, $itemsTotal);
            $slugPath = $this->getSlugPath($collection, $page);
            $targetFile = $this->getDestinationPath($slugPath.'/index.html');
            $current = $this->getCurrent($collection, $page);
            $pageData = $this->getPageData($targetFile, $slugPath.'/index.html', $current);
            $data = $this->getRenderData($collection, $page, $pageData, $pagination, $pagesRequired);

            $html = $this->fileManager->render($collection->indexTemplate, $data);

            if ($this->fileManager->save($targetFile, $html) !== false) {
                $outputFrom = $this->getSourcePath($collection->slug, true);
                $outputTo = $this->getDestinationPath($slugPath.'/index.html', true);
                $this->output->file('Index page: '.$outputFrom.' => '.$outputTo);
            }
        }
    }

    private function getSlugPath(Collection $collection, int $page): string
    {
        return $page > 1 ? $collection->slug.'/page/'.$page : $collection->slug;
    }

    private function getCurrent(Collection $collection, int $page): string
    {
        return $page != 1 ? $collection->slug.'/page/'.$page : '/'.$collection->slug.'/';
    }

    private function getPageData(string $targetFile, string $relativeTargetFile, string $current): Page
    {
        return new Page(
            path: $targetFile,
            relativePath: $relativeTargetFile,
            permalink: $current,
            url: url($current),
        );
    }

    private function getRenderData(Collection $collection, int $page, Page $pageData, CollectionPaginator $pagination, int $pagesRequired): RenderContext
    {
        $items = $collection->items();

        usort($items, function (CollectionItem $a, CollectionItem $b) {
            return $b->date <=> $a->date;
        });

        $collection = $collection->setItems($items)->slice(($page - 1) * $collection->pageSize, $collection->pageSize);

        return new RenderContext(
            site: $this->getSite(),
            page: $pageData,
            collection: $collection,
            pagination: $pagesRequired > 1 ? $pagination : null
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
