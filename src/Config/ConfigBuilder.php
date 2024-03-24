<?php

namespace Terdelyi\Phanstatic\Config;

use Terdelyi\Phanstatic\Builders\Collection\Collection;

class ConfigBuilder
{
    /**
     * @var array<string,mixed>
     */
    private array $config;

    public function __construct()
    {
        $this->config = [
            'workDir' => dirname(__DIR__),
            'sourceDir' => 'content',
            'buildDir' => 'dist',
        ];
    }

    public function setWorkDir(string $workDir): self
    {
        $this->config['workDir'] = $workDir;

        return $this;
    }

    public function setSourceDir(string $sourceDir): self
    {
        $this->config['sourceDir'] = $sourceDir;

        return $this;
    }

    public function setBuildDir(string $buildDir): self
    {
        $this->config['buildDir'] = $buildDir;

        return $this;
    }

    /**
     * @param CollectionConfig[] $collections
     */
    public function addCollection(string $key, ?string $title, ?string $slug, ?int $pageSize): self
    {
        $this->config['collections'][$key] = new CollectionConfig($title, $slug, $pageSize);

        return $this;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->config['baseUrl'] = $baseUrl;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->config['title'] = $title;

        return $this;
    }

    /**
     * @param array<string,mixed> $metaAttributes
     */
    public function setMeta(array $metaAttributes): self
    {
        $this->config['meta'] = $metaAttributes;

        return $this;
    }

    public function build(): Config
    {
        return new Config($this->config);
    }
}
