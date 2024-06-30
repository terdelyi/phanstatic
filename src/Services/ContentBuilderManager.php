<?php

namespace Terdelyi\Phanstatic\Services;

use Terdelyi\Phanstatic\ContentBuilders\BuilderContext;
use Terdelyi\Phanstatic\ContentBuilders\BuilderInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Support\Output\OutputInterface;

class ContentBuilderManager
{
    private float $startTime;
    private float $endTime;

    public function __construct(
        private readonly OutputInterface $output,
        private readonly Config          $config,
    ) {}

    public function run(): void
    {
        $context = new BuilderContext($this->output, $this->config);

        $this->startTime = microtime(true);

        /** @var BuilderInterface $builder */
        foreach ($this->config->getBuilders() as $builder) {
            $builder = new $builder();
            $builder->build($context);

            $this->output->space();
        }

        $this->endTime = microtime(true);
    }

    public function getExecutionTime(): float
    {
        return round($this->endTime - $this->startTime, 4);
    }
}
