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
}
