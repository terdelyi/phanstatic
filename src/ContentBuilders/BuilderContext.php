<?php

namespace Terdelyi\Phanstatic\ContentBuilders;

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\FileManagerInterface;
use Terdelyi\Phanstatic\Support\OutputInterface;

class BuilderContext implements BuilderContextInterface
{
    public function __construct(
        private readonly OutputInterface      $output,
        private readonly Config               $config,
        private readonly FileManagerInterface $fileManager,
    ) {}

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getFileManager(): FileManagerInterface
    {
        return $this->fileManager;
    }
}
