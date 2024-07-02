<?php

namespace Terdelyi\Phanstatic\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Config\Config;

class PreviewCommand extends Command
{
    private Config $config;

    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setName('preview')
            ->setDescription('Start built-in PHP server to preview the output directory')
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

    /**
     * @param OutputInterface&\Terdelyi\Phanstatic\Support\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = [
            'host' => $input->getOption('host') ?? 'localhost',
            'port' => $input->getOption('port') ?? 8000,
        ];

        $this->validateOptions($options);

        $resultCode = 0;
        $command = sprintf("php -S %s:%s -t %s", $options['host'], $options['port'], $this->config->getBuildDir());

        passthru($command, $resultCode);

        return $resultCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * @param array<string,string|int> $options
     * @return void
     */
    private function validateOptions(array $options): void
    {
        if (!is_string($options['host'])) {
            throw new InvalidArgumentException('Host must be a string');
        }

        if (!is_int($options['port'])) {
            throw new InvalidArgumentException('Port must be an integer');
        }
    }
}
