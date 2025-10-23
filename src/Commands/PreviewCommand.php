<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\Support\Helpers;

class PreviewCommand extends Command
{
    public function __construct(
        private readonly CommandLineExecutor $executor,
        private readonly Helpers $helpers,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('preview')
            ->setDescription('Start built-in PHP server to preview your site')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Hostname or ip address', 'localhost')
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'Port', 8080)
            ->addOption('dist', 'd', InputOption::VALUE_NONE, 'Preview the built files from the dist folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicDir = $this->helpers->getBuildDir();
        $sourceDir = $this->helpers->getSourceDir();
        $options = $this->validate($input);

        if ($options['dist'] && ! file_exists($publicDir)) {
            $output->writeln("<error>Directory {$publicDir} does not exist. Have you run build before?</error>");

            return Command::FAILURE;
        }

        $source = $options['dist']
            ? "-t {$publicDir}"
            : "-t {$sourceDir}".' '.__DIR__.'/../server.php';
        $host = $options['host'].':'.$options['port'];

        $command = "BASE_URL=\"http://{$host}\" php -S {$host} {$source}";
        $outcome = $this->executor->run($command);

        return $outcome === false ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @return array<mixed|string>
     */
    private function validate(InputInterface $input): array
    {
        return [
            'host' => (string) $input->getOption('host'),
            'port' => (int) $input->getOption('port'),
            'dist' => (bool) $input->getOption('dist'),
        ];
    }
}
