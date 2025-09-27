<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New;

use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\New\Commands\BuildCommand;
use Terdelyi\Phanstatic\New\Commands\ConfigCommand;
use Terdelyi\Phanstatic\New\Commands\PreviewCommand;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Readers\FileReader;
use Terdelyi\Phanstatic\New\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\New\Support\ConfigBuilder;
use Terdelyi\Phanstatic\New\Support\ConfigLoader;
use Terdelyi\Phanstatic\New\Support\Helpers;
use Terdelyi\Phanstatic\New\Support\Time;

class Phanstatic
{
    public static string $workingDir;
    private string $name = 'Phanstatic';
    private string $version = '1.0.0';
    private static ?ContainerBuilder $container = null;

    public function __construct(string $workingDir)
    {
        self::$workingDir = $workingDir;
    }

    public function init(): void
    {
        try {
            $container = self::getContainer();

            $this->registerServices($container);
            $this->registerCommands($container);
            $this->registerGenerators($container);

            $container->compile();

            $this->loadConsoleApplication($container);
        } catch (\Throwable $e) {
            echo 'Error: '.$e->getMessage().PHP_EOL;

            exit(Command::FAILURE);
        }
    }

    public static function getContainer(): ContainerBuilder
    {
        return self::$container ??= new ContainerBuilder();
    }

    private function registerServices(ContainerBuilder $container): void
    {
        $configFilePath = self::$workingDir.'/'.ConfigBuilder::$defaultPath;
        $container->register(ConfigLoader::class, ConfigLoader::class)
            ->addArgument($configFilePath);

        $container->register(Config::class, Config::class)
            ->setFactory([new Reference(ConfigLoader::class), 'load']);

        $container->register(SymfonyConsole::class, SymfonyConsole::class)
            ->addArgument($this->name)
            ->addArgument($this->version)
            ->setPublic(true);

        $container->register(Time::class, Time::class);
        $container->register(CommandLineExecutor::class, CommandLineExecutor::class);
        $container->register(Finder::class, Finder::class);
        $container->register(OutputInterface::class, Output::class)->setAutowired(true);

        $container->autowire(Filesystem::class, Filesystem::class);
        $container->autowire(FileReader::class, FileReader::class);
        $container->autowire(Helpers::class, Helpers::class)
            ->addArgument(new Reference(Config::class))
            ->addArgument(self::$workingDir);
    }

    private function registerCommands(ContainerBuilder $container): void
    {
        foreach ($this->getCommands() as $command) {
            $container->autowire($command, $command)
                ->setPublic(true)
                ->addTag('command');
        }
    }

    /**
     * @return array<int, string>
     */
    private function getCommands(): array
    {
        return [
            BuildCommand::class,
            PreviewCommand::class,
            ConfigCommand::class,
        ];
    }

    private function registerGenerators(ContainerBuilder $container): void
    {
        /** @var Config $config */
        $config = $container->get(Config::class);

        foreach ($config->generators as $generator) {
            $container->autowire($generator, $generator)
                ->setPublic(true)
                ->addTag('command');
        }
    }

    /**
     * @throws \Exception
     */
    private function loadConsoleApplication(ContainerBuilder $container): void
    {
        $commands = [];
        foreach ($this->getCommands() as $commandId) {
            /** @var Command $command */
            $command = $container->get($commandId);
            $commands[] = $command;
        }

        /** @var SymfonyConsole $application */
        $application = $container->get(SymfonyConsole::class);
        $application->addCommands($commands);
        $application->setCatchErrors();
        $application->setCatchExceptions(true);
        $application->run();
    }
}
