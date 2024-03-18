<?php

namespace Terdelyi\Phanstatic\Data;

class Page
{
    public function __construct(
        public string $path,
        public string $permalink,
        public string $url,
        public ?string $title = null,
        public ?string $content = null,
        private array $meta = [],
    )
    {
    }

    public function __get($key): mixed
    {
        return $this->meta[$key] ?? null;
    }

    public function is($permalink): bool
    {
        return str_starts_with($this->permalink, $permalink);
    }
}