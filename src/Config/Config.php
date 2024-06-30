<?php

namespace Terdelyi\Phanstatic\Config;

use RuntimeException;
use Terdelyi\Phanstatic\ContentBuilders\Asset\AssetBuilder;
use Terdelyi\Phanstatic\ContentBuilders\Collection\CollectionBuilder;
use Terdelyi\Phanstatic\ContentBuilders\Page\PageBuilder;

class Config
{
    private string $sourceDir;
    private string $buildDir;
    /** @var CollectionConfig[] */
    private array $collections;
    private string $baseUrl;
    private string $title;
    /** @var array<string,mixed> */
    private array $meta;
    /**
     * @var string[]
     */
    private array $builders;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        $this->sourceDir = $config['sourceDir'] ?? throw new RuntimeException('Source directory is not set');
        $this->buildDir = $config['buildDir'] ?? throw new RuntimeException('Build directory is not set');
        $this->title = $config['title'] ?? '';
        $this->baseUrl = $config['baseUrl'] ?? '';
        $this->meta = $config['meta'] ?? [];
        $this->collections = $config['collections'] ?? [];
        $this->builders = $config['builders'] ?? [];
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

    /**
     * @return string[]
     */
    public function getBuilders(): array
    {
        if (count($this->builders) > 0) {
            return $this->builders;
        }

        return [
            PageBuilder::class,
            AssetBuilder::class,
            CollectionBuilder::class,
        ];
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
    public function getCollections(?string $key = null): CollectionConfig|array
    {
        if ($key !== null && isset($this->collections[$key])) {
            return $this->collections[$key];
        }

        return $this->collections;
    }
}
