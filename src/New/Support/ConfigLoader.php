<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Terdelyi\Phanstatic\New\Models\Config;

class ConfigLoader
{
    public function __construct(private readonly ?string $configFile = null) {}

    public function load(): Config
    {
        if (!$this->configFile) {
            return ConfigBuilder::make()
                ->build();
        }

        if (!file_exists($this->configFile)) {
            throw new \RuntimeException('Cannot load config file: '.$this->configFile);
        }

        return require $this->configFile;
    }
}
