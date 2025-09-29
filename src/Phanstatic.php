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
use Terdelyi\Phanstatic\Support\ConfigBuilder;
use Terdelyi\Phanstatic\Support\ConfigLoader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\Time;

class Phanstatic
{
    public string $workingDir;
    public readonly Config $config;
    public readonly Helpers $helpers;
    private string $name = 'Phanstatic';
    private string $version = '1.0.0';
    private static ?Phanstatic $instance = null;

    public function __construct(string $workingDir, Config $config, Helpers $helpers)
    {
        $this->workingDir = $workingDir;
        $this->config = $config;
        $this->helpers = $helpers;
    }

    public static function init(?string $workingDir = null, ?Config $config = null, ?Helpers $helpers = null): self
    {
        $workingDir ??= getcwd();

        if ( ! $workingDir) {
            throw new \RuntimeException('Working directory cannot be read');
        }

        if ($config === null) {
            $configFilePath = $workingDir.'/'.ConfigBuilder::$defaultPath;
            $config = (new ConfigLoader($configFilePath))->load();
        }

        if ($helpers === null) {
            $helpers = new Helpers($config, $workingDir);
        }

        return self::$instance = new self($workingDir, $config, $helpers);
    }

    public static function get(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Phanstatic not initialized. Call Phanstatic::init() first.');
        }

        return self::$instance;
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $application = new SymfonyConsole($this->name, $this->version);
        $application->addCommands($this->commands());
        $application->setDefaultCommand('config');
        $application->setCatchErrors();
        $application->setCatchExceptions(true);
        $application->run();
    }

    /**
     * @return array<int, Command>
     */
    private function commands(): array
    {
        return [
            new BuildCommand(self::get()->config, self::get()->helpers, new Filesystem(), new Time()),
            new PreviewCommand(new CommandLineExecutor(), self::get()->helpers),
            new ConfigCommand(self::get()->config, self::get()->helpers),
        ];
    }
}
