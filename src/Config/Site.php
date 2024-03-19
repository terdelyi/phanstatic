<?php

namespace Terdelyi\Phanstatic\Config;

class Site
{
    public function __construct(private array $config) {}

    public function __get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }
}
