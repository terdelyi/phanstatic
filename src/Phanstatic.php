<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic;

use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;
use Terdelyi\Phanstatic\Commands\BuildCommand;
use Terdelyi\Phanstatic\Commands\ConfigCommand;
use Terdelyi\Phanstatic\Commands\PreviewCommand;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\Support\ConfigLoader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\Time;

class Phanstatic
{
    private string $name = 'Phanstatic';
    private string $version = '1.0.0';

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        if ( ! $this->loadConfig()) {
            return;
        }

        $application = new SymfonyConsole($this->name, $this->version);
        $application->addCommands($this->commands());
        $application->setDefaultCommand('config');
        $application->setCatchErrors();
        $application->setCatchExceptions(true);
        $application->run();
    }

    public function loadConfig(): bool
    {
        $workingDir = getcwd() ?: null;
        $contentDir = $workingDir.'/'.Config::DEFAULT_SOURCE;

        if ( ! is_dir($contentDir)) {
            echo 'The content directory is missing. Are you in the project path?'.PHP_EOL;

            return false;
        }

        $configFilePath = $workingDir.'/'.Config::DEFAULT_PATH;
        $configLoaded = (new ConfigLoader())->load($configFilePath);

        if ( ! $configLoaded) {
            return false;
        }

        return true;
    }

    /**
     * @return array<int, Command>
     */
    private function commands(): array
    {
        $config = Config::get();
        $helpers = new Helpers($config);

        return [
            new BuildCommand($config, $helpers, new Filesystem(), new Time()),
            new PreviewCommand(new CommandLineExecutor(), $helpers),
            new ConfigCommand($config, $helpers),
        ];
    }
}
