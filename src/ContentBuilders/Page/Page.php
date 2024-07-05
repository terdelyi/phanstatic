<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders\Page;

class Page
{
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
        private readonly array $meta = [],
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
