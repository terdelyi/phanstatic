<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Models\Config;

class Helpers
{
    private readonly Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? Config::get();
    }

    public function getBaseUrl(?string $permalink = null): string
    {
        if ($permalink !== null) {
            $permalink = ! \str_starts_with($permalink, '/') ? "/{$permalink}" : $permalink;

            return $this->config->baseUrl.$permalink;
        }

        return $this->config->baseUrl;
    }

    public function getAsset(string $permalink): string
    {
        $permalink = ! \str_starts_with($permalink, '/') ? "/{$permalink}" : $permalink;
        $permalink = '/assets'.$permalink;

        return $this->getBaseUrl($permalink);
    }

    public function getBaseDir(?string $path = null): string
    {
        $workingDir = $this->config->path;

        if ($path !== null) {
            return "{$workingDir}/{$path}";
        }

        return $workingDir;
    }

    public function getSourceDir(?string $path = null, bool $relative = false): string
    {
        $sourceDir = $relative ? $this->config->sourceDir : "{$this->config->workingDir}/{$this->config->sourceDir}";

        if ($path !== null) {
            return "{$sourceDir}/{$path}";
        }

        return $sourceDir;
    }

    public function getBuildDir(?string $path = null, bool $relative = false): string
    {
        $buildDir = $relative ? $this->config->buildDir : "{$this->config->workingDir}/{$this->config->buildDir}";

        if ($path !== null) {
            return "{$buildDir}/{$path}";
        }

        return $buildDir;
    }
}
