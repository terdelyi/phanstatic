<?php

namespace Terdelyi\Phanstatic\Data;

class CollectionItem
{
    public function __construct(
        public string $title,
        public string $link,
        public string $excerpt,
        public string $date,
    ) {}
}
