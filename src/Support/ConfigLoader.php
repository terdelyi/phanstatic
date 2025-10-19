<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Models\Config;

class ConfigLoader
{
    public function load(?string $workingDir = null): ?Config
    {
        $configPath = $workingDir.'/'.Config::DEFAULT_PATH;
        $configFile = file_exists($configPath) ? $configPath : null;

        if ( ! $configFile) {
            $config = Config::init();
            $config->workingDir = $workingDir;

            return $config;
        }

        try {
            /** @var Config $config */
            $config = require $configFile;
            $config->path = $configPath;
            $config->workingDir = $workingDir;

            Config::init($config);

            return $config;
        } catch (\Throwable $ex) {
            echo 'Invalid config file content. Please return a Config object in '.$configFile.PHP_EOL;

            return null;
        }
    }
}
