<?php

namespace Terdelyi\Phanstatic\Builders\Collection;

class CollectionItem
{
    public function __construct(
        public string $title,
        public string $link,
        public string $excerpt,
        public string $date,
    ) {}
}
