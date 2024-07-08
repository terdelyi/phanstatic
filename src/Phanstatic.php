<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\ConfigBuilder;
use Terdelyi\Phanstatic\Console\BuildCommand;
use Terdelyi\Phanstatic\Console\ConfigCommand;
use Terdelyi\Phanstatic\Console\PreviewCommand;
use Terdelyi\Phanstatic\Support\Output;

class Phanstatic
{
    private string $name = 'Phanstatic';
    private string $version = '0.5.0';
    private string $defaultConfigFile = 'content/config.php';
    private static ?Config $config = null;

    public function init(): void
    {
        try {
            $config = $this->loadConfig();

            $application = new Application($this->name, $this->version);
            $application->addCommands([
                new BuildCommand($config),
                new PreviewCommand($config),
                new ConfigCommand($config),
            ]);
            $application->setCatchErrors();
            $application->setCatchExceptions(true);

            // TODO: Add custom output formatter here
            $application->run(
                output: new Output()
            );
        } catch (\Throwable $e) {
            echo 'Error: '.$e->getMessage().PHP_EOL;

            exit(Command::FAILURE);
        }
    }

    public static function getConfig(): Config
    {
        if (self::$config === null) {
            return ConfigBuilder::make()
                ->build();
        }

        return self::$config;
    }

    /**
     * @throws \RuntimeException
     */
    private function loadConfig(): Config
    {
        $currentDirectory = getcwd();
        $configFile = $currentDirectory.'/'.$this->defaultConfigFile;

        if (file_exists($configFile)) {
            $config = require $configFile;

            self::setConfig($config);
        }

        return self::getConfig();
    }

    private static function setConfig(Config $config): void
    {
        self::$config = $config;
    }
}
