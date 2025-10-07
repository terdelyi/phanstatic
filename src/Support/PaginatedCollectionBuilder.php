<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CollectionItem;
use Terdelyi\Phanstatic\Models\CollectionPaginator;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;

class PaginatedCollectionBuilder
{
    private int $totalItems;
    private int $totalPages;

    public function __construct(private readonly Collection $collection, private int $page)
    {
        $this->totalItems = $collection->count();
        $this->totalPages = (int) ceil($this->totalItems / $collection->pageSize);
    }

    public static function build(Collection $collection, int $page = 1): self
    {
        return new self($collection, $page);
    }

    public function path(?string $suffix = null): string
    {
        $path = $this->collection->slug;
        if ($this->page > 1) {
            $path .= '/page/'.$this->page;
        }

        return $path.$suffix;
    }

    public function context(Site $site): CompilerContext
    {
        $pagination = $this->totalPages > 1
            ? CollectionPaginator::create($this->page, $this->totalPages, $this->collection->slug, $this->totalItems)
            : null;
        $permalink = $this->path('/');
        $path = $this->path('/index.html');
        $pageData = new Page(
            path: $path,
            relativePath: build_dir($path),
            permalink: $permalink,
            url: url($permalink),
        );

        return new CompilerContext(
            site: $site,
            page: $pageData,
            collection: $this->spliceCollection(),
            pagination: $pagination,
        );
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
