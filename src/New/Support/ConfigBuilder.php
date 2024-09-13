<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Terdelyi\Phanstatic\New\Models\Config;

class ConfigBuilder
{
    /**
     * @var array<string,mixed>
     */
    private array $config;

    private function __construct()
    {
        $this->config = $this->getDefaultConfig();
    }

    public static function make(): ConfigBuilder
    {
        return new self();
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
     * @param array<int,string> $builders
     */
    public function setBuilders(array $builders): self
    {
        $this->config['builders'] = $builders;

        return $this;
    }

    public function build(): Config
    {
        return new Config(
            $this->config['sourceDir'],
            $this->config['buildDir'],
            $this->config['baseUrl'],
            $this->config['title'],
            $this->config['meta'],
            $this->config['collections'],
            $this->config['generators'],
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfig(): array
    {
        return [
            'sourceDir' => 'content',
            'buildDir' => 'dist',
            'baseUrl' => 'http://localhost:8000',
            'title' => '',
            'meta' => [],
            'collections' => [],
            'generators' => [],
        ];
    }
}
