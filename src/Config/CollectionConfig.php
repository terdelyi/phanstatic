<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Config;

class CollectionConfig
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $slug,
        public readonly ?int $pageSize,
    ) {}
}
