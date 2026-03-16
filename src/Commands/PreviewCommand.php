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
    private string $sourceDir;
    private string $buildDir;

    public function __construct(
        private readonly CommandLineExecutor $executor,
        private readonly Helpers $helpers,
    ) {
        parent::__construct();

        $this->sourceDir = $this->helpers->getSourceDir();
        $this->buildDir = $this->helpers->getBuildDir();
    }

    protected function configure(): void
    {
        $this->setName('preview')
            ->setDescription('Start built-in PHP server to preview your site')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Hostname or ip address', 'localhost')
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'Port', 8080)
            ->addOption('scheme', 's', InputOption::VALUE_REQUIRED, 'URL scheme', 'http')
            ->addOption('dist', 'd', InputOption::VALUE_NONE, 'Preview the built files from the dist folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $this->validate($input);

        if ($options === null) {
            $output->writeln('<error>Invalid host or port provided.</error>');

            return Command::FAILURE;
        }

        if ($options['dist'] && ! file_exists($this->buildDir)) {
            $output->writeln("<error>Directory {$this->buildDir} does not exist. Have you run build before?</error>");

            return Command::FAILURE;
        }

        return $this->serve($options) === false
            ? Command::FAILURE
            : Command::SUCCESS;
    }

    /**
     * @param array{host: string, port: int, scheme: string, dist: bool} $options
     */
    private function serve(array $options): ?bool
    {
        $server = __DIR__.'/../server.php';
        $source = $options['dist']
            ? "-t {$this->buildDir}"
            : "-t {$this->sourceDir} {$server}";
        $host = "{$options['host']}:{$options['port']}";

        $command = "BASE_URL=\"{$options['scheme']}://{$host}\" php -S {$host} {$source}";

        return $this->executor->run($command);
    }

    /**
     * @return null|array{host: string, port: int, scheme: string, dist: bool}
     */
    private function validate(InputInterface $input): ?array
    {
        $host = (string) $input->getOption('host');
        $port = (int) $input->getOption('port');
        $scheme = (string) $input->getOption('scheme');

        if ( ! preg_match('/^[a-zA-Z0-9.\-]+$/', $host)) {
            return null;
        }

        if ($port < 1 || $port > 65535) {
            return null;
        }

        if ( ! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return [
            'host' => $host,
            'port' => $port,
            'scheme' => $scheme,
            'dist' => (bool) $input->getOption('dist'),
        ];
    }
}
