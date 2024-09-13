<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New;

use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Terdelyi\Phanstatic\Console\BuildCommand;
use Terdelyi\Phanstatic\Console\ConfigCommand;
use Terdelyi\Phanstatic\Console\PreviewCommand;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\ConfigLoader;

class Phanstatic
{
    private string $name = 'Phanstatic';
    private string $version = '1.0.0';
    private string $defaultConfigFile = 'content/config.php';
    private static ?ContainerBuilder $container = null;

    public function __construct(public readonly string $workingDir) {}

    public function init(): void
    {
        try {
            $container = self::getContainer();
            $this->registerServices($container);
            $this->registerCommands($container);
            $this->loadConsoleApplication($container);
        } catch (\Throwable $e) {
            echo 'Error: '.$e->getMessage().PHP_EOL;

            exit(Command::FAILURE);
        }
    }

    public static function getContainer(): ContainerBuilder
    {
        if (self::$container !== null) {
            return self::$container;
        }

        return self::$container = new ContainerBuilder();
    }

    private function registerServices(ContainerBuilder $container): void
    {
        $configFile = $this->workingDir.'/'.$this->defaultConfigFile;
        $config = new ConfigLoader($configFile);

        $container->set(Config::class, $config);
        $container->register(SymfonyConsole::class, SymfonyConsole::class)
            ->addArgument($this->name)
            ->addArgument($this->version);
    }

    private function registerCommands(ContainerBuilder $container): void
    {
        $commands = [
            BuildCommand::class,
            PreviewCommand::class,
            ConfigCommand::class,
        ];

        foreach ($commands as $command) {
            $container->register($command, $command)
                ->addArgument(new Reference(Config::class))
                ->addTag('command');
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
