<?php

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput implements OutputInterface
{
    public function header(string $message): void
    {
        $this->writeln(["<info>{$message}</info>", str_repeat('=', strlen($message)), '']);
    }

    public function warning(string $message): void
    {
        $this->writeln("<warning>{$message}</warning>");
    }

    public function error(string $message): void
    {
        $this->writeln("<error>{$message}</error>");
    }

    public function action(string $message): void
    {
        $this->writeln("<info>{$message}</info>");
    }

    public function file(string $message): void
    {
        $this->writeln("- {$message}");
    }

    public function time(string $message): void
    {
        $this->writeln("<comment>{$message}</comment>");
    }

    public function space(): void
    {
        $this->writeln('');
    }
}
