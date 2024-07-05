<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders\Collection;

class CollectionItem
{
    public function __construct(
        public string $title,
        public string $link,
        public string $excerpt,
        public string $date,
    ) {}
}
