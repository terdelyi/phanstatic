<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property OutputInterface $output The output instance.
 */
trait OutputHelper
{
    public function logo(): void
    {
        $this->output->writeln('______ _                     _        _   _');
        $this->output->writeln('| ___ \ |                   | |      | | (_)');
        $this->output->writeln('| |_/ / |__   __ _ _ __  ___| |_ __ _| |_ _  ___');
        $this->output->writeln('|  __/| \'_ \ / _` | \'_ \/ __| __/ _` | __| |/ __|');
        $this->output->writeln('| |   | | | | (_| | | | \__ \ || (_| | |_| | (__ ');
        $this->output->writeln('\_|   |_| |_|\__,_|_| |_|___/\__\__,_|\__|_|\___|');
    }

    public function text(string $message): void
    {
        $this->output->writeln($message);
    }

    public function item(string $message): void
    {
        $this->output->writeln(' • '.$message);
    }

    public function lines(int $lines = 1): void
    {
        for ($i = 0; $i < $lines; ++$i) {
            $this->output->writeln('');
        }
    }
}
