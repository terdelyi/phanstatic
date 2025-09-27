<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property OutputInterface $output The output instance.
 */
trait OutputHelper
{
    public function text(string $message): void
    {
        $this->output->writeln($message);
    }

    public function item(string $message): void
    {
        $this->output->writeln('  • '.$message);
    }

    public function lines(int $lines = 1): void
    {
        for ($i = 0; $i < $lines; ++$i) {
            $this->output->writeln('');
        }
    }
}
