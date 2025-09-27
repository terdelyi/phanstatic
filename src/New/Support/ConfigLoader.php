<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Terdelyi\Phanstatic\New\Models\Config;

class ConfigLoader
{
    public function __construct(
        private readonly string $configPath
    ) {}

    public function load(): Config
    {
        var_dump(file_exists($this->configPath));
        $configFile = file_exists($this->configPath) ? $this->configPath : null;

        if (!$configFile) {
            return ConfigBuilder::make()
                ->setNoConfig()
                ->build();
        }

        return require $configFile;
    }
}
