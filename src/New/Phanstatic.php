<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New;

use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Terdelyi\Phanstatic\New\Commands\BuildCommand;
use Terdelyi\Phanstatic\New\Commands\PreviewCommand;
use Terdelyi\Phanstatic\New\Commands\ConfigCommand;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\New\Support\ConfigLoader;
use Terdelyi\Phanstatic\New\Support\FileManager;
use Terdelyi\Phanstatic\New\Support\Helpers;
use Terdelyi\Phanstatic\New\Support\Time;

class Phanstatic
{
    public static string $workingDir;
    private string $name = 'Phanstatic';
    private string $version = '1.0.0';
    private static ?ContainerBuilder $container = null;
    private string $defaultConfigFile = 'content/config.php';

    public function __construct(string $workingDir)
    {
        self::$workingDir = $workingDir;
    }

    public function init(): void
    {
        try {
            $container = self::getContainer();
            $this->registerServices($container);
            $this->registerHelpers($container);
            $this->registerCommands($container);
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
        $defaultConfigFile = self::$workingDir.'/'.$this->defaultConfigFile;
        $configFile = file_exists($defaultConfigFile) ? $defaultConfigFile : null;
        $config = new ConfigLoader($configFile);

        $container->set(Config::class, $config->load());
        $container->register(SymfonyConsole::class, SymfonyConsole::class)
            ->addArgument($this->name)
            ->addArgument($this->version);

        $container->register(Time::class, Time::class);

        $container->register(FileManager::class, FileManager::class);
    }

    private function registerHelpers(ContainerBuilder $container): void
    {
        $container->register(Helpers::class, Helpers::class)
            ->addArgument(new Reference(Config::class))
            ->addArgument(self::$workingDir);
    }

    private function registerCommands(ContainerBuilder $container): void
    {
        $commands = [
            BuildCommand::class => [
                new Reference(Config::class),
                new Reference(Helpers::class),
                new Reference(FileManager::class),
                new Reference(Time::class),
            ],
            PreviewCommand::class => [
                new Reference(CommandLineExecutor::class),
                new Reference(Helpers::class),
            ],
            ConfigCommand::class => [
                new Reference(Config::class),
                new Reference(Helpers::class),
            ],
        ];

        foreach ($commands as $command => $arguments) {
            $registeredCommand = $container->register($command, $command)
                ->addTag('command');

            foreach ($arguments as $argument) {
                $registeredCommand->addArgument($argument);
            }
        }
    }

    /**
     * @return array<int, string>
     *
     * @throws \Exception
     */
    private function getCommands(): array
    {
        $commands = self::getContainer()->findTaggedServiceIds('command');

        return array_keys($commands);
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
