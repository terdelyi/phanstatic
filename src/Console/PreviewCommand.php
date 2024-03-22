<?php

namespace Terdelyi\Phanstatic\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\FileManager;

class PreviewCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('preview')
            ->setDescription('Launches built-in PHP server to preview the dist directory')
            ->addOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'Hostname or ip address',
                'localhost',
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_REQUIRED,
                'Port',
                8000,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileManager = new FileManager(new Filesystem(), new Finder());
        $documentRoot = $fileManager->getDestinationFolder();
        $options = $input->getOptions();

        if (!is_string($options['host'])) {
            throw new InvalidArgumentException('Host must be a string');
        }

        if (!is_int($options['port'])) {
            throw new InvalidArgumentException('Host must be a string');
        }

        $host = $options['host'] ?: 'localhost';
        $port = $options['port'] ?: 8000;
        $resultCode = 0;

        passthru(sprintf("php -S %s %s -t %s", $host, $port, $documentRoot), $resultCode);

        return $resultCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
