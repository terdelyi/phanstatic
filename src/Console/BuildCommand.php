<?php

namespace Terdelyi\Phanstatic\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\ContentBuilderManager;
use Terdelyi\Phanstatic\Support\Output\Output;

class BuildCommand extends Command
{
    private Config $config;

    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setName('build')
            ->setDescription('Build the static files into the output directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $wrappedOutput = new Output($output);
        $buildManager = new ContentBuilderManager($wrappedOutput, $this->config);

        $wrappedOutput->header("Build started");

        $buildManager->run();

        $wrappedOutput->time("Build completed in {$buildManager->getExecutionTime()} seconds");

        return Command::SUCCESS;
    }
}
