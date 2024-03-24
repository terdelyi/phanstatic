<?php

namespace Terdelyi\Phanstatic\Builders;

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Console\Output\BuildOutputInterface;
use Terdelyi\Phanstatic\Services\FileManager;

interface BuilderInterface
{
    public function __construct(
        FileManager          $fileManager,
        BuildOutputInterface $output,
        Config               $config,
    );

    public function build(): void;
}
