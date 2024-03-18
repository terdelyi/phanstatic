<?php

namespace Terdelyi\Phanstatic\Config;

use RuntimeException;

class Config
{
    private static ?Config $instance = null;
    private string $workingDirectory;

    private Site $site;

    private array $collections;

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public function setWorkDir(string $path): void
    {
        $this->workingDirectory = $path;
    }

    public function loadFromFile(string $configFile): void
    {
        if (!file_exists($configFile)) {
            throw new RuntimeException('Config file not found');
        }

        $config = require $configFile;

        if (!is_array($config)) {
            throw new RuntimeException('Config variables are not in an array');
        }

        if (isset($config['site'])) {
            $this->setSite($config['site']);
        }

        if (isset($config['collections'])) {
            $this->setCollections($config['collections']);
        }
    }

    public function getWorkDir(): string
    {
        return $this->workingDirectory;
    }

    public function setSite(array $site): void
    {
        $this->site = new Site($site);
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setCollections(array $collections): void
    {
        $parsedCollection = [];

        foreach ($collections as $collection) {
            $parsedCollection[] = new Collection(
                $collection['title'] ?? null,
                $collection['slug'] ?? null,
                $collection['pageSize'] ?? null
            );
        }

        $this->collections = $parsedCollection;
    }

    /**
     * @return Collection|Collection[]
     */
    public function getCollections(string $key = null): Collection|array
    {
        if ($key !== null && isset($this->collections[$key])) {
            return $this->collections[$key];
        }

        return $this->collections;
    }
}