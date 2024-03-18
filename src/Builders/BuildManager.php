<?php

namespace Terdelyi\Phanstatic\Builders;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\Builders\Asset\AssetBuilder;
use Terdelyi\Phanstatic\Builders\Collection\CollectionBuilder;
use Terdelyi\Phanstatic\Builders\Page\PageBuilder;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Console\Output\BuildOutputInterface;
use Terdelyi\Phanstatic\Services\FileManager;

class BuildManager
{
    private float $startTime;
    private float $endTime;

    public function __construct(
        private readonly BuildOutputInterface $output,
        private readonly Config               $config,
    )
    {
    }

    public function run(): void
    {
        $fileManager = new FileManager(new Filesystem(), new Finder());

        $this->startTime = microtime(true);

        /** @var BuilderInterface $builder */
        foreach ($this->getBuilders() as $builder) {
            $builder = new $builder($fileManager, $this->output, $this->config);
            $builder->build();

            $this->output->space();
        }

        $this->endTime = microtime(true);
    }

    public function getExecutionTime(): float
    {
        return round($this->endTime - $this->startTime, 4);
    }

    private function getBuilders(): array
    {
        return [
            PageBuilder::class,
            AssetBuilder::class,
            CollectionBuilder::class,
        ];
    }
}