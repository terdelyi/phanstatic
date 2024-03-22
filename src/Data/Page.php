<?php

namespace Terdelyi\Phanstatic\Data;

class Page
{
    /**
     * @param array<string,mixed> $meta
     */
    public function __construct(
        public string          $path,
        public string          $permalink,
        public string          $url,
        public ?string         $title = null,
        public ?string         $content = null,
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
