<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Models\Config;

class ConfigLoader
{
    /**
     * @throws \Exception
     */
    public function load(string $workingDir, ?string $customConfigPath = null): ?Config
    {
        $defaultConfigPath = $workingDir.'/'.Config::DEFAULT_PATH;

        if ( ! $customConfigPath && ! file_exists($defaultConfigPath)) {
            $config = new Config(
                workingDir: $workingDir,
            );
            Config::init($config);

            return $config;
        }

        $customConfigPath ??= Config::DEFAULT_PATH;
        $configFilePath = $workingDir.'/'.$customConfigPath;

        if ( ! file_exists($configFilePath)) {
            throw new \Exception("Config file at {$configFilePath} does not exist.");
        }

        try {
            $config = require $configFilePath;
        } catch (\Throwable $ex) {
            throw new \Exception("Invalid config file initialisation in {$configFilePath}");
        }

        if ( ! $config instanceof Config) {
            throw new \Exception("Invalid config file content in {$configFilePath}. Please return a Config object.");
        }

        $config->path = $customConfigPath;
        $config->workingDir = $workingDir;

        return Config::init($config);
    }
}
