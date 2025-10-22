<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class Page
{
    public const TYPE_PAGE = 'page';
    public const TYPE_COLLECTION = 'collection';
    public const TYPE_COLLECTION_SINGLE = 'collection-single';

    /**
     * @param array<string,mixed> $meta
     */
    public function __construct(
        public string $path,
        public string $relativePath,
        public string $permalink,
        public string $url,
        public ?string $title = null,
        public ?string $content = null,
        public array $meta = [],
        public string $type = self::TYPE_PAGE,
    ) {}

    public function __get(string $key): mixed
    {
        return $this->meta[$key] ?? null;
    }

    public function is(string $permalink): bool
    {
        return str_starts_with($this->permalink, $permalink);
    }
}
