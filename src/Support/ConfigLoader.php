<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Models\Config;

class ConfigLoader
{
    public function load(?string $configPath = null): ?Config
    {
        $configFile = $configPath && file_exists($configPath) ? $configPath : null;

        if ( ! $configFile) {
            return Config::init();
        }

        try {
            /** @var Config $config */
            $config = require $configFile;
            $config->path = $configPath;

            Config::init($config);

            return $config;
        } catch (\Throwable $ex) {
            echo 'Invalid config file content. Please return a Config object in '.$configFile.PHP_EOL;

            return null;
        }
    }
}
