<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Terdelyi\Phanstatic\New\Generators\GeneratorInterface;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Phanstatic;
use Terdelyi\Phanstatic\New\Support\Helpers;
use Terdelyi\Phanstatic\New\Support\Time;

class BuildCommand extends Command
{
    private Config $config;
    private Helpers $helpers;
    private Filesystem $filesystem;
    private Time $time;

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
            ->addOption('no-clean', null, InputOption::VALUE_NONE, 'Do not clean the build directory before building');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('no-clean')) {
            $output->writeln(['Cleaning out build directory....', '']);
            $this->filesystem->remove($this->helpers->getBuildDir());
        }

        $startTime = $this->time->getCurrentTime();
        $container = Phanstatic::getContainer();

        foreach ($this->config->generators as $generator) {
            ///** @var GeneratorInterface $generatorInstance */
            //$generatorInstance = $container->get($generator);
            //$generatorInstance->run();
            // Run builder
            $output->writeln($generator);
        }

        $executionTime = round($this->time->getCurrentTime() - $startTime, 4);

        $output->writeln("Build completed in {$executionTime} seconds");

        return Command::SUCCESS;
    }
}
