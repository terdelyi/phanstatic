<?php

namespace Terdelyi\Phanstatic\Config;

use RuntimeException;

class Config
{
    private string $workDir;
    private string $sourceDir;
    private string $buildDir;
    /**
     * @var CollectionConfig[]
     */
    private array $collections;
    private string $baseUrl;
    private string $title;
    /**
     * @var array<string,mixed>
     */
    private array $meta;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        $this->workDir = $config['workDir'] ?? throw new RuntimeException('Work directory is not set');
        $this->sourceDir = $config['sourceDir'] ?? throw new RuntimeException('Source directory is not set');
        $this->buildDir = $config['buildDir'] ?? throw new RuntimeException('Build directory is not set');
        $this->title = $config['title'] ?? '';
        $this->baseUrl = $config['baseUrl'] ?? '';
        $this->meta = $config['meta'] ?? [];
        $this->collections = $config['collections'] ?? [];
    }

    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    public function getSourceDir(?string $path = null): string
    {
        if ($path !== null) {
            return $this->sourceDir . $path;
        }

        return $this->sourceDir;
    }

    public function getBuildDir(?string $path = null): string
    {
        if ($path !== null) {
            return $this->buildDir . $path;
        }

        return $this->buildDir;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBaseUrl(?string $permalink = null): string
    {
        if ($permalink !== null) {
            return $this->baseUrl . $permalink;
        }

        return $this->baseUrl;
    }

    /**
     * @return array<string,mixed>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @return CollectionConfig|CollectionConfig[]
     */
    public function getCollections(?string $key): CollectionConfig|array
    {
        if ($key !== null && isset($this->collections[$key])) {
            return $this->collections[$key];
        }

        return $this->collections;
    }
}
