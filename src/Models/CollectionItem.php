<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class CollectionItem
{
    /** @param array<string,mixed> $meta */
    public function __construct(
        public string $title,
        public string $link,
        public string $excerpt,
        public string $date,
        public array $meta = [],
    ) {}

    public static function fromPage(Page $page): self
    {
        // @TODO: Date could be part of page - filemtime or meta
        $date = $page->meta['date'];
        unset($page->meta['date']);

        return new self(
            title: $page->title ?? '',
            link: $page->url ?? '',
            excerpt: $page->description ?? '',
            date: $date,
            meta: $page->meta,
        );
    }
}
