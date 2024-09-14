<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\Helpers;
use Terdelyi\Phanstatic\New\Support\FileManager;
use Terdelyi\Phanstatic\New\Support\Time;

class BuildCommand extends Command
{
    private Config $config;
    private Helpers $helpers;
    private FileManager $fileManager;
    private Time $time;

    public function __construct(Config $config, Helpers $helpers, FileManager $fileManager, Time $time)
    {
        parent::__construct();

        $this->config = $config;
        $this->helpers = $helpers;
        $this->fileManager = $fileManager;
        $this->time = $time;
    }

    protected function configure(): void
    {
        $this->setName('build')
            ->setDescription('Build static files into the output directory')
            ->addOption('no-clean', null, InputOption::VALUE_NONE, 'Do not clean the build directory before building');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('no-clean')) {
            $output->writeln(['Cleaning out build directory....', '']);
            $this->fileManager->cleanDirectory($this->helpers->getBuildDir());
        }

        $startTime = $this->time->getCurrentTime();

        foreach ($this->config->generators as $generator) {
            // Run builder
            $output->writeln($generator);
        }

        $executionTime = round($this->time->getCurrentTime() - $startTime, 4);

        $output->writeln("<green>Build completed in {$executionTime} seconds</green>");

        return Command::SUCCESS;
    }
}
