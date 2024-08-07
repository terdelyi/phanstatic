<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\ContentBuilders\ContentBuilderManager;
use Terdelyi\Phanstatic\Models\BuilderContext;
use Terdelyi\Phanstatic\Services\FileManager;
use Terdelyi\Phanstatic\Support\OutputInterface;

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
            ->setDescription('Build the static files into the output directory')
            ->addOption(
                'no-clean',
                null,
                InputOption::VALUE_NONE,
                'Do not clean the build directory before building'
            );
    }

    /**
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, SymfonyOutputInterface $output): int
    {
        if (!$output instanceof OutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $fileSystem = new FileManager();

        if (!$input->getOption('no-clean')) {
            $fileSystem->cleanDirectory($this->config->getBuildDir());
            $output->writeln(['Build directory has been wiped out.', '']);
        }

        $context = new BuilderContext($output, $this->config, $fileSystem);
        $builders = $this->config->getBuilders();
        $buildManager = new ContentBuilderManager($context);

        $startTime = microtime(true);
        $buildManager->run($builders);
        $executionTime = round(microtime(true) - $startTime, 4);

        $output->time("Build completed in {$executionTime} seconds");

        return Command::SUCCESS;
    }
}
