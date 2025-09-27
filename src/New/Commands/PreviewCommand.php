<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\New\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\New\Support\Helpers;

class PreviewCommand extends Command
{
    public function __construct(
        private CommandLineExecutor $executor,
        private Helpers $helpers,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('preview')
            ->setDescription('Start built-in PHP server to preview the output directory')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Hostname or ip address', 'localhost')
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'Port', 8000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicDir = $this->helpers->getBuildDir();

        if (!file_exists($publicDir)) {
            $output->writeln("<error>Directory {$publicDir} does not exist. Have you run build before?</error>");

            return Command::FAILURE;
        }

        $options = $this->validate($input);
        $command = "php -S {$options['host']}:{$options['port']} -t {$this->helpers->getBuildDir()}";
        $outcome = $this->executor->run($command);

        return $outcome === false ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @return array<mixed|string>
     */
    private function validate(InputInterface $input): array
    {
        $options = [
            'host' => $input->getOption('host') ?? 'localhost',
            'port' => $input->getOption('port') ?? 8000,
        ];

        if (!is_string($options['host'])) {
            throw new \InvalidArgumentException('Host must be a string');
        }

        if (!is_int($options['port'])) {
            throw new \InvalidArgumentException('Port must be an integer');
        }

        return $options;
    }
}
