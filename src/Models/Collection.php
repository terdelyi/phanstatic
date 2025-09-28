<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class Collection
{
    /** @var CollectionItem[] */
    private array $items;

    /**
     * @param CollectionItem[] $items
     */
    public function __construct(
        public string $title,
        public string $basename,
        public string $sourceDir,
        public string $slug,
        public string $singleTemplate,
        public string $indexTemplate,
        array $items = [],
        public int $pageSize = 10,
    ) {
        $this->items = $items;
    }

    public function add(CollectionItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @param CollectionItem[] $items
     */
    public function cloneWithItems(array $items): Collection
    {
        return new Collection(
            $this->title,
            $this->basename,
            $this->sourceDir,
            $this->slug,
            $this->singleTemplate,
            $this->indexTemplate,
            $items,
            $this->pageSize
        );
    }

    /**
     * @return CollectionItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}