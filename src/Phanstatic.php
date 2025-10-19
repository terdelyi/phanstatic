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

    public function __construct(private string $basePath) {}

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->checkContentDirectoryExist();
        $this->loadConfig();

        $application = new SymfonyConsole($this->name, $this->version);
        $application->addCommands($this->commands());
        $application->setDefaultCommand('config');
        $application->setCatchErrors();
        $application->setCatchExceptions(true);
        $application->run();
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @throws \Exception
     */
    public function loadConfig(): void
    {
        $configLoaded = (new ConfigLoader())->load($this->basePath);

        if ( ! $configLoaded) {
            throw new \Exception('Could not load configuration file');
        }
    }

    /**
     * @throws \Exception
     */
    private function checkContentDirectoryExist(): void
    {
        $contentDir = $this->basePath.'/'.Config::DEFAULT_SOURCE;

        if ( ! is_dir($contentDir)) {
            throw new \Exception('The content directory is missing. Are you in the project path?');
        }
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
