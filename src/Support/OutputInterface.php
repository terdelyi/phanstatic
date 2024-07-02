<?php

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Console\Output\ConsoleOutputInterface;

interface OutputInterface extends ConsoleOutputInterface
{
    public function header(string $message): void;

    public function warning(string $message): void;

    public function error(string $message): void;

    public function action(string $message): void;

    public function file(string $message): void;

    public function space(): void;

    public function time(string $message): void;
}