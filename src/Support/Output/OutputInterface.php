<?php

namespace Terdelyi\Phanstatic\Support\Output;

use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;

interface OutputInterface
{
    public function __construct(SymfonyOutputInterface $output);

    public function header(string $message): void;

    public function time(string $message): void;

    public function action(string $message): void;

    public function file(string $message): void;

    public function warning(string $message): void;

    public function error(string $message): void;

    public function space(): void;
}
