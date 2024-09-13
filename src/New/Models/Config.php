<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Models;

class Config
{
    public function __construct(
        public readonly string $sourceDir,
        public readonly string $buildDir,
        public readonly string $baseUrl,
        public readonly string $title,
        /** @var array<string,mixed> */
        public readonly array $meta,
        /** @var array<int,CollectionConfig> */
        public readonly array $collections,
        /** @var array<int,string> */
        public readonly array $generators,
    ) {}

    // TODO: Should be moved to a helper
    /*
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
    */

    // TODO: Should be moved to a helper
    /**
    public function getBaseUrl(?string $permalink = null): string
    {
        if ($permalink !== null) {
            return $this->baseUrl.$permalink;
        }

        return $this->baseUrl;
    }**/

}
