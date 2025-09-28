<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class Config
{
    public function __construct(
        public readonly string $sourceDir,
        public readonly string $buildDir,
        public readonly string $baseUrl,
        public readonly string $title,
        /** @var array<string,mixed> */
        public readonly array $meta,
        /** @var array<int,CollectionConfig> */
        public readonly array $collections,
        /** @var array<int,string> */
        public readonly array $generators,
        public readonly ?string $path = null,
    ) {}
}
