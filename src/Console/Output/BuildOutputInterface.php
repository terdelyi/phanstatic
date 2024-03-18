<?php

namespace Terdelyi\Phanstatic\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;

interface BuildOutputInterface
{
    public function __construct(OutputInterface $output);

    public function header(string $message): void;

    public function time(string $message): void;

    public function action(string $message): void;

    public function file(string $message): void;

    public function warning(string $message): void;

    public function error(string $message): void;

    public function space(): void;
}