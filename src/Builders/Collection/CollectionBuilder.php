<?php

namespace Terdelyi\Phanstatic\Builders\Collection;

use DateTimeInterface;
use Exception;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Builders\BuilderInterface;
use Terdelyi\Phanstatic\Builders\Page\Page;
use Terdelyi\Phanstatic\Builders\RenderContext;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\SiteConfig;
use Terdelyi\Phanstatic\Console\Output\BuildOutputInterface;
use Terdelyi\Phanstatic\Services\FileManager;
use Throwable;

class CollectionBuilder implements BuilderInterface
{
    private string $sourcePath = '/collections';
    private string $destinationPath;

    public function __construct(
        private readonly FileManager          $fileManager,
        private readonly BuildOutputInterface $output,
        private readonly Config               $config,
    ) {
        $this->sourcePath = $this->config->getSourceDir($this->sourcePath);
        $this->destinationPath = $this->config->getBuildDir();
    }

    /**
     * @throws Throwable
     */
    public function build(): void
    {
        $this->output->action("Looking for collections...");

        $collections = $this->fileManager->getDirectories($this->sourcePath, '== 0');

        foreach ($collections as $directory) {
            $collection = $this->createCollection($directory);

            if (!$this->fileManager->exists($collection->singleTemplate)) {
                throw new Exception("Collection must have a single template: " . $collection->singleTemplate);
            }

            $this->output->action(ucfirst($collection->basename) . ' collection found. Looking for items...');

            $files = $this->fileManager->getFiles($collection->sourceDir, '*.md');

            if ($files->count() === 0) {
                $this->output->warning('No items to copy in' . $collection->basename);

                continue;
            }

            foreach ($files as $file) {
                [$page, $data] = $this->buildSinglePages($file, $collection);

                $item = new CollectionItem(
                    title: $page->title ?? '',
                    link: $page->url ?? '',
                    excerpt: $data->page->description ?? '',
                    date: $data->page->date ?? date(DateTimeInterface::ATOM, $file->getMTime())
                );

                $collection->add($item);
            }

            if ($this->fileManager->exists($collection->indexTemplate)) {
                $this->buildIndexPages($collection);
            }
        }
    }

    /**
     * @throws CommonMarkException
     * @throws Exception
     */
    private function getPage(SplFileInfo $file, string $collectionSlug): Page
    {
        $fileData = $this->getFileData($file->getBasename('.md'), $collectionSlug);
        $fileContent = file_get_contents($file->getPathname());

        if (!$fileContent) {
            throw new Exception('File is empty: ' . $file->getPathname());
        }

        $parsedFile = YamlFrontMatter::parse((string) $fileContent);
        $meta = $parsedFile->matter();
        $title = $parsedFile->matter('title');
        $body = (new CommonMarkConverter())->convert($parsedFile->body());
        unset($meta['title']);

        return new Page(
            path: $fileData['path'],
            permalink: $fileData['permalink'],
            url: url($fileData['permalink']),
            title: $title,
            content: $body,
            meta: $meta
        );
    }

    /**
     * @return array<string,string>
     */
    private function getFileData(string $basename, string $collectionSlug): array
    {
        $permalink = '/' . $collectionSlug . "/{$basename}/";

        if ($basename === 'index') {
            $permalink = '/';
        }

        $newPath = $this->destinationPath . "{$permalink}index.html";

        if ($permalink === '/') {
            $newPath = $this->destinationPath . "/index.html";
        }

        return [
            'path' => $newPath,
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
            singleTemplate: $directory->getPathname() . '/single.php',
            indexTemplate: $directory->getPathname() . '/index.php',
            items: [],
            pageSize: $collectionConfig->pageSize ?? 10
        );
    }

    /**
     * @return array{Page, RenderContext}
     * @throws CommonMarkException
     * @throws Exception|Throwable
     */
    private function buildSinglePages(SplFileInfo $file, Collection $collection): array
    {
        $page = $this->getPage($file, $collection->slug);
        $data = new RenderContext(
            site: $this->getSite(),
            page: $page,
        );

        if ($this->fileManager->save($page->path, $this->fileManager->render($collection->singleTemplate, $data)) !== false) {
            $this->output->file($file->getPathname() . ' => ' . $page->path);
        }

        return [$page, $data];
    }

    /**
     * @throws Throwable
     */
    private function buildIndexPages(Collection $collection): void
    {
        $total = $collection->count();
        $pagesRequired = (int) ceil($total / $collection->pageSize);

        for ($page = 1; $page <= $pagesRequired; $page++) {
            $pagination = $this->getPagination($page, $pagesRequired, $collection->slug, $total);
            $slugPath = $this->getSlugPath($collection, $page);
            $targetFile = $this->getTargetFile($slugPath);
            $current = $this->getCurrent($collection, $page);
            $pageData = $this->getPageData($targetFile, $current);
            $data = $this->getRenderData($collection, $page, $pageData, $pagination, $pagesRequired);

            $html = $this->fileManager->render($collection->indexTemplate, $data);

            if ($this->fileManager->save($targetFile, $html) !== false) {
                $this->output->file($this->sourcePath . '/' . $collection->slug . ' => ' . $targetFile);
            }
        }
    }

    private function getSlugPath(Collection $collection, int $page): string
    {
        return $page > 1 ? $collection->slug . '/page/' . $page : $collection->slug;
    }

    private function getTargetFile(string $slugPath): string
    {
        return $this->destinationPath . '/' . $slugPath . '/index.html';
    }

    private function getCurrent(Collection $collection, int $page): string
    {
        return $page != 1 ? $collection->slug . '/page/' . $page : '/' . $collection->slug . '/';
    }

    private function getPageData(string $targetFile, string $current): Page
    {
        return new Page(
            path: $targetFile,
            permalink: $current,
            url: url($current),
        );
    }

    private function getRenderData(Collection $collection, int $page, Page $pageData, Pagination $pagination, int $pagesRequired): RenderContext
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

    public function getPagination(int $page, int $pagesRequired, string $slug, int $total): Pagination
    {
        $shouldHaveNextPage = $page >= 1 && $page < $pagesRequired;
        $nextSlug = $slug . '/page/' . ($page + 1);

        $shouldHavePreviousPage = $page !== 1;
        $previousSlug = $page === 2 ? $slug : "{$slug}/page/" . ($page - 1);

        return new Pagination(
            next: $shouldHaveNextPage ? url($nextSlug) : null,
            previous: $shouldHavePreviousPage ? url($previousSlug) : null,
            current: $page,
            total: $total,
            isLast: $page === $pagesRequired,
        );
    }

    /**
     * @return SiteConfig
     */
    public function getSite(): SiteConfig
    {
        return new SiteConfig(
            title: $this->config->getTitle(),
            baseUrl: $this->config->getBaseUrl(),
            meta: $this->config->getMeta(),
        );
    }
}
