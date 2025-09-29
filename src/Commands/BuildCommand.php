<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\Time;

class BuildCommand extends Command
{
    private Config $config;
    private Helpers $helpers;
    private Filesystem $filesystem;
    private Time $time;
    private float $startedAt;

    public function __construct(
        Config $config,
        Helpers $helpers,
        Filesystem $filesystem,
        Time $time,
    ) {
        parent::__construct();

        $this->config = $config;
        $this->helpers = $helpers;
        $this->filesystem = $filesystem;
        $this->time = $time;
    }

    protected function configure(): void
    {
        $this->setName('build')
            ->setDescription('Build static files into the output directory')
            ->addOption(
                'no-clean',
                null,
                InputOption::VALUE_NONE,
                'Do not clean the build directory before building'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startBuild($input, $output);

        $this->runGenerators($input, $output);

        return $this->completeBuild($output);
    }

    private function startBuild(InputInterface $input, OutputInterface $output): void
    {
        if ( ! $input->getOption('no-clean')
            && $this->filesystem->exists($this->helpers->getBuildDir())
        ) {
            $output->writeln(['Cleaning out build directory....', '']);
            $this->filesystem->remove($this->helpers->getBuildDir());
        }

        $this->startTimer();
    }

    private function startTimer(): void
    {
        $this->startedAt = $this->time->getCurrentTime();
    }

    private function runGenerators(InputInterface $input, OutputInterface $output): void
    {
        foreach ($this->config->generators as $generator) {
            (new $generator())->run($input, $output);
        }
    }

    private function stopTimer(): float
    {
        return round($this->time->getCurrentTime() - $this->startedAt, 4);
    }

    private function completeBuild(OutputInterface $output): int
    {
        $executionTime = $this->stopTimer();

        $output->writeln("Build completed in {$executionTime} seconds");

        return Command::SUCCESS;
    }
}
