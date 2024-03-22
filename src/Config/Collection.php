<?php

namespace Terdelyi\Phanstatic\Config;

class Collection
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $slug,
        public readonly ?int $pageSize,
    ) {}
}
