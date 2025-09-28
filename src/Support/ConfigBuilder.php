<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Terdelyi\Phanstatic\New\Generators\AssetGenerator;
use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use Terdelyi\Phanstatic\New\Models\Config;

class ConfigBuilder
{
    public static string $defaultPath = 'content/config.php';

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
            $this->config['path'],
        );
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
     * @param array<int,string> $generators
     */
    public function setGenerators(array $generators): self
    {
        $this->config['generators'] = $generators;

        return $this;
    }

    public function addGenerator(string $generator): self
    {
        $this->config['generators'][] = $generator;

        return $this;
    }

    public function addCollection(string $directory, ?string $title, ?string $slug, ?int $pageSize): self
    {
        $this->config['collections'][$directory] = new CollectionConfig($title, $slug, $pageSize);

        return $this;
    }

    public function setNoConfig(): self
    {
        $this->config['path'] = null;

        return $this;
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
            'generators' => [
                AssetGenerator::class,
            ],
            'path' => self::$defaultPath,
        ];
    }
}
