<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic;

use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Reference;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\ConfigBuilder;
use Terdelyi\Phanstatic\Console\BuildCommand;
use Terdelyi\Phanstatic\Console\ConfigCommand;
use Terdelyi\Phanstatic\Console\PreviewCommand;
use Terdelyi\Phanstatic\Support\Container;
use Terdelyi\Phanstatic\Support\Output;

class Phanstatic
{
    private string $name = 'Phanstatic';
    private string $version = '0.6.0';
    private string $defaultConfigFile = 'content/config.php';

    public function init(): void
    {
        try {
            $this->registerServices();
            $this->getCommands();

            // TODO: Add custom output formatter here
            $application = $this->getApplication();
            $application->run(
                output: new Output()
            );
        } catch (\Throwable $e) {
            echo 'Error: '.$e->getMessage().PHP_EOL;

            exit(Command::FAILURE);
        }
    }

    /**
     * TODO: Could this go to ConfigBuilder or should we have a separate ConfigLoader class?
     *
     * @throws \RuntimeException
     */
    private function loadConfig(): Config
    {
        $currentDirectory = getcwd();

        if (!$currentDirectory) {
            throw new \RuntimeException('Cannot find the working directory');
        }

        $configFile = $currentDirectory.'/'.$this->defaultConfigFile;

        if (file_exists($configFile)) {
            return require $configFile;
        }

        return ConfigBuilder::make()
            ->build();
    }

    private function registerServices(): void
    {
        $container = Container::getInstance();

        $container->set('config', $this->loadConfig());
        $container->register(SymfonyConsole::class, SymfonyConsole::class)
            ->addArgument($this->name)
            ->addArgument($this->version);
        $container->register(BuildCommand::class, BuildCommand::class)
            ->addArgument(new Reference('config'))
            ->addTag('command');
        $container->register(PreviewCommand::class, PreviewCommand::class)
            ->addArgument(new Reference('config'))
            ->addTag('command');
        $container->register(ConfigCommand::class, ConfigCommand::class)
            ->addArgument(new Reference('config'))
            ->addTag('command');
    }

    /**
     * @return array<int, Command>
     *
     * @throws \Exception
     */
    private function getCommands(): array
    {
        $container = Container::getInstance();

        return [
            $container->get(BuildCommand::class),
            $container->get(PreviewCommand::class),
            $container->get(ConfigCommand::class),
        ];
    }

    /**
     * @throws \Exception
     */
    private function getApplication(): SymfonyConsole
    {
        $container = Container::getInstance();

        /** @var SymfonyConsole $application */
        $application = $container->get(SymfonyConsole::class);
        $application->addCommands($this->getCommands());
        $application->setCatchErrors();
        $application->setCatchExceptions(true);

        return $application;
    }
}
