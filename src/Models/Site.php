<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class Site
{
    /**
     * @param array<string,mixed> $meta
     */
    public function __construct(
        public readonly string $title,
        public readonly string $baseUrl,
        private readonly array $meta = []
    ) {}

    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    public static function fromConfig(Config $config): Site
    {
        return new self($config->title, $config->baseUrl, $config->meta);
    }

    public function get(string $key): mixed
    {
        return $this->meta[$key] ?? null;
    }
}
