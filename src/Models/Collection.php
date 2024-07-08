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
    public function setItems(array $items): Collection
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return CollectionItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function slice(int $from, int $to): Collection
    {
        $result = array_slice($this->items, $from, $to);

        return $this->setItems($result);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
