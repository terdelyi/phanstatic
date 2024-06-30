<?php

namespace Terdelyi\Phanstatic\ContentBuilders;

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Support\Output\OutputInterface;

class BuilderContext implements BuilderContextInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly Config          $config
    ) {}

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
