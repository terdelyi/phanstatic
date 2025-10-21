<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators\Collection;

use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\CollectionPaginator;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Support\Helpers;

class IndexContextBuilder
{
    private Helpers $helpers;
    private Config $config;

    private int $totalItems;
    private int $totalPages;

    public function __construct(
        private readonly Collection $collection,
        private int $page = 1,
        ?Helpers $helpers = null,
        ?Config $config = null
    ) {
        $this->totalItems = $collection->count();
        $this->totalPages = (int) ceil($this->totalItems / $collection->pageSize);
        $this->helpers = $helpers ?? new Helpers();
        $this->config = $config ?? Config::get();
    }

    public function build(): CompilerContext
    {
        $page = $this->getPage();

        $site = new Site(
            title: $this->config->title,
            baseUrl: $this->config->baseUrl,
            meta: $this->config->meta
        );

        $pagination = $this->totalPages > 1
            ? CollectionPaginator::create($this->page, $this->totalPages, $this->collection->slug, $this->totalItems)
            : null;

        $context = new CompilerContext($site, $page);
        $context->collection = $this->spliceCollection();
        $context->pagination = $pagination;

        return $context;
    }

    public function getPage(): Page
    {
        $permalink = $this->path('/');
        $path = $this->path('/index.html');

        return new Page(
            path: $this->helpers->getBuildDir($path),
            relativePath: $path,
            permalink: $permalink,
            url: url($permalink),
        );
    }

    private function path(?string $suffix = null): string
    {
        $path = $this->collection->slug;
        if ($this->page > 1) {
            $path .= '/page/'.$this->page;
        }

        return $path.$suffix;
    }

    private function spliceCollection(): Collection
    {
        $items = $this->collection->items();
        usort($items, fn (CollectionItem $a, CollectionItem $b) => $b->date <=> $a->date);
        $offset = $this->page - 1;
        $slicedItems = array_slice($items, $offset * $this->collection->pageSize, $this->collection->pageSize);

        return $this->collection->cloneWithItems($slicedItems);
    }
}
