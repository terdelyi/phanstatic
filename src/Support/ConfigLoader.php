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

        if (!$configFile) {
            return ConfigBuilder::make()
                ->setNoConfig()
                ->build();
        }

        return require $configFile;
    }
}
