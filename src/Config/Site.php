<?php

namespace Terdelyi\Phanstatic\Config;

class Site
{
    /**
     * @param array<string,mixed> $config
     */
    public function __construct(private readonly array $config) {}

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function __get(string $key): mixed
    {
        return $this->get($key);
    }


}
