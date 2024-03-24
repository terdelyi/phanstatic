<?php

namespace Terdelyi\Phanstatic;

use Symfony\Component\Console\Application;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\ConfigBuilder;
use Terdelyi\Phanstatic\Console\BuildCommand;
use Terdelyi\Phanstatic\Console\PreviewCommand;
use Terdelyi\Phanstatic\Services\Container;

class Phanstatic
{
    private string $name = 'Phanstatic';
    private string $version = '0.1.0';
    private string $configFile = 'content/config.php';

    public function __construct(
        private readonly string $workDir
    ) {}

    public function init(): void
    {
        $config = $this->getConfig();

        Container::set('config', $config);

        $application = new Application($this->name, $this->version);
        $application->addCommands([
            new BuildCommand($config),
            new PreviewCommand(),
        ]);
        $application->setCatchErrors();
        $application->setCatchExceptions(true);
        $application->run();
    }

    private function getConfig(): Config
    {
        $configFile = $this->workDir . '/' . $this->configFile;

        if (file_exists($configFile)) {
            return require $configFile;
        }

        $configBuilder = new ConfigBuilder();

        return $configBuilder
            ->setWorkDir($this->workDir)
            ->build();
    }
}
