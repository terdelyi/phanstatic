<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Config;

use Terdelyi\Phanstatic\ContentBuilders\AssetBuilder;
use Terdelyi\Phanstatic\ContentBuilders\BuilderInterface;
use Terdelyi\Phanstatic\ContentBuilders\CollectionBuilder;
use Terdelyi\Phanstatic\ContentBuilders\PageBuilder;

class Config
{
    private string $workDir;
    private string $sourceDir;
    private string $buildDir;

    /** @var CollectionConfig[] */
    private array $collections;
    private string $baseUrl;
    private string $title;

    /** @var array<string,mixed> */
    private array $meta;

    /**
     * @var class-string<BuilderInterface>[]
     */
    private array $builders;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        $this->workDir = $config['workDir'] ?? throw new \RuntimeException('Working directory must set');
        $this->sourceDir = $config['sourceDir'] ?? throw new \RuntimeException('Source directory is not set');
        $this->buildDir = $config['buildDir'] ?? throw new \RuntimeException('Build directory is not set');
        $this->title = $config['title'] ?? '';
        $this->baseUrl = $config['baseUrl'] ?? '';
        $this->meta = $config['meta'] ?? [];
        $this->collections = $config['collections'] ?? [];
        $this->builders = $config['builders'] ?? [];
    }

    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    public function getSourceDir(?string $path = null, bool $relative = false): string
    {
        $sourceDir = $relative ? $this->sourceDir : $this->workDir.'/'.$this->sourceDir;

        if ($path !== null) {
            return $sourceDir.'/'.$path;
        }

        return $sourceDir;
    }

    public function getBuildDir(?string $path = null, bool $relative = false): string
    {
        $buildDir = $relative ? $this->buildDir : $this->workDir.'/'.$this->buildDir;

        if ($path !== null) {
            return $buildDir.'/'.$path;
        }

        return $buildDir;
    }

    /**
     * @return class-string<BuilderInterface>[]
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
            return $this->baseUrl.$permalink;
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
    public function getCollections(?string $key = null): array|CollectionConfig
    {
        if ($key !== null && isset($this->collections[$key])) {
            return $this->collections[$key];
        }

        return $this->collections;
    }
}
