<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Models\Config;

class ConfigLoader
{
    public function __construct(
        private readonly string $configPath
    ) {}

    public function load(): Config
    {
        $configFile = file_exists($this->configPath) ? $this->configPath : null;

        if ( ! $configFile) {
            return ConfigBuilder::make()
                ->setPath($configFile)
                ->build();
        }

        /** @var ?ConfigBuilder $builder */
        $builder = require $configFile;

        if ( ! is_object($builder)) {
            throw new \RuntimeException('Invalid config file content. Please return a ConfigBuilder.');
        }

        $builder->setPath($configFile);

        return $builder->build();
    }
}
