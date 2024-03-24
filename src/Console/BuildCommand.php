<?php

namespace Terdelyi\Phanstatic\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Console\Output\BuildOutput;
use Terdelyi\Phanstatic\Services\BuildManager;

class BuildCommand extends Command
{
    public function __construct(
        private readonly Config $config
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('build')
            ->setDescription('Build site distribution');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new BuildOutput($output);
        $buildManager = new BuildManager($output, $this->config);

        $output->header("Build started");

        $buildManager->run();

        $output->time("Build completed in {$buildManager->getExecutionTime()} seconds");

        return Command::SUCCESS;
    }
}
