<?php

namespace Terdelyi\Phanstatic\Support\Output;

use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;

class Output implements OutputInterface
{
    public function __construct(
        private readonly SymfonyOutputInterface $output,
    ) {}

    public function header(string $message): void
    {
        $this->output->writeln(["<info>{$message}</info>", str_repeat('=', strlen($message)), '']);
    }

    public function warning(string $message): void
    {
        $this->output->writeln("<warning>{$message}</warning>");
    }

    public function error(string $message): void
    {
        $this->output->writeln("<error>{$message}</error>");
    }

    public function action(string $message): void
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    public function file(string $message): void
    {
        $this->output->writeln("- {$message}");
    }

    public function time(string $message): void
    {
        $this->output->writeln("<comment>{$message}</comment>");
    }

    public function space(): void
    {
        $this->output->writeln('');
    }
}
