<?php

namespace Terdelyi\Phanstatic;

use Symfony\Component\Console\Application;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\ConfigBuilder;
use Terdelyi\Phanstatic\Console\BuildCommand;
use Terdelyi\Phanstatic\Console\PreviewCommand;
use Terdelyi\Phanstatic\Console\ShowConfigCommand;
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
            $this->loadConfig($this->defaultConfigFile);
            $config = $this->getConfig();

            $application = new Application($this->name, $this->version);
            $application->addCommands([
                new BuildCommand($config),
                new PreviewCommand($config),
                new ShowConfigCommand($config),
            ]);
            $application->setCatchErrors();
            $application->setCatchExceptions(true);

            // TODO: Add custom output formatter here
            $application->run(
                output: new Output()
            );
        } catch (\Throwable $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        }
    }

    public static function getConfig(): Config
    {
        if (self::$config === null) {
            return (new ConfigBuilder())
                ->setBaseUrl('http://localhost')
                ->build();
        }

        return self::$config;
    }

    /**
     * @throws \RuntimeException
     */
    private function loadConfig(string $file): void
    {
        $configFile = dirname(__DIR__) . '/' . $file;

        if (file_exists($configFile)) {
            $config = require $configFile;

            self::setConfig($config);
        }
    }

    private static function setConfig(Config $config): void
    {
        self::$config = $config;
    }
}
