<?php

namespace Terdelyi\Phanstatic\Config;

class ConfigBuilder
{
    /**
     * @var array<string,mixed>
     */
    private array $config;

    public function __construct()
    {
        $this->config = [
            'baseUrl' => 'http://localhost',
            'sourceDir' => 'content',
            'buildDir' => 'dist',
            'workDir' => $this->getWorkDir(),
        ];
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

    public function addCollection(string $key, ?string $title, ?string $slug, ?int $pageSize): ConfigBuilder
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

    /**
     * @param array<mixed> $builders
     */
    public function setBuilders(array $builders): self
    {
        $this->config['builders'] = $builders;

        return $this;
    }

    public function build(): Config
    {
        return new Config($this->config);
    }

    private function getWorkDir(): string
    {
        $workDir = getcwd();

        if ($workDir === false) {
            throw new \RuntimeException('Could not determine the current working directory');
        }

        return $workDir;
    }
}
