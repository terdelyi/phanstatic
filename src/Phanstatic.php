<?php

namespace Terdelyi\Phanstatic;

use RuntimeException;
use Symfony\Component\Console\Application;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Console\BuildCommand;
use Terdelyi\Phanstatic\Console\PreviewCommand;

class Phanstatic
{
    public function __construct(
        private readonly string $workDir,
    )
    {
    }

    public function init(): void
    {
        $config = Config::getInstance();
        $config->setWorkDir($this->workDir);

        $application = new Application('Phanstatic');
        $application->addCommands([
            new BuildCommand($config),
            new PreviewCommand(),
        ]);
        $application->setCatchErrors();
        $application->setCatchExceptions(true);
        $application->run();
    }
}