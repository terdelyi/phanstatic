<?php

namespace Terdelyi\Phanstatic\Builders\Collection;

use DateTime;
use DateTimeInterface;
use Exception;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Builders\BuilderInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Data\Collection;
use Terdelyi\Phanstatic\Data\CollectionItem;
use Terdelyi\Phanstatic\Data\RenderData;
use Terdelyi\Phanstatic\Data\Page;
use Terdelyi\Phanstatic\Data\Pagination;
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
        $this->sourcePath = $this->fileManager->getSourceFolder($this->sourcePath);
        $this->destinationPath = $this->fileManager->getDestinationFolder();
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
                    title: $page->title,
                    link: $page->url,
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
     */
    private function getPage(SplFileInfo $file, string $collectionSlug): Page
    {
        $fileData = $this->getFileData($file->getBasename('.md'), $collectionSlug);
        $parsedFile = YamlFrontMatter::parse(file_get_contents($file->getPathname()));
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

    private function buildSinglePages(mixed $file, Collection $collection): array
    {
        $page = $this->getPage($file, $collection->slug);
        $data = new RenderData(
            site: $this->config->getSite(),
            page: $page,
        );

        if ($this->fileManager->save($page->path, $this->fileManager->render($collection->singleTemplate, $data)) !== false) {
            $this->output->file($file->getPathname() . ' => ' . $page->path);
        }

        return [$page, $data];
    }

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

    private function getRenderData(Collection $collection, int $page, Page $pageData, Pagination $pagination, int $pagesRequired): RenderData
    {
        $items = $collection->items();

        usort($items, function (CollectionItem $a, CollectionItem $b) {
            return $b->date <=> $a->date;
        });

        $collection = $collection->setItems($items)->slice(($page - 1) * $collection->pageSize, $collection->pageSize);

        return new RenderData(
            site: $this->config->getSite(),
            page: $pageData,
            collection: $collection,
            pagination: $pagesRequired > 1 ? $pagination : null
        );
    }

    public function getPagination(int $page, int $pagesRequired, string $slug, int $total): Pagination
    {
        $next = $page >= 1 && $page < $pagesRequired ? $slug . '/page/' . $page + 1 : null;
        $previous = $page != 1 ? $slug . ($page === 2 ? '' : '/page/' . $page - 1) : null;

        return new Pagination(
            next: $next !== null ? url($next) : null,
            previous: $previous !== null ? url($previous) : null,
            current: $page,
            total: $total,
            isLast: $page === $pagesRequired,
        );
    }
}
