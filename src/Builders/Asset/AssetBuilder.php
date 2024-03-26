<?php

namespace Terdelyi\Phanstatic\Builders\Asset;

use Terdelyi\Phanstatic\Builders\BuilderInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Console\Output\BuildOutputInterface;
use Terdelyi\Phanstatic\Services\FileManager;

class AssetBuilder implements BuilderInterface
{
    private string $sourcePath = '/assets';
    private string $destinationPath = '/assets';

    public function __construct(
        private readonly FileManager          $fileManager,
        private readonly BuildOutputInterface $output,
        private readonly Config               $config,
    ) {
        $this->sourcePath = $this->config->getSourceDir($this->sourcePath);
        $this->destinationPath = $this->config->getBuildDir($this->destinationPath);
    }

    public function build(): void
    {
        if (!$this->fileManager->exists($this->sourcePath)) {
            $this->output->action("Skipping assets: no 'content/assets' directory found");

            return;
        }

        $this->output->action("Looking for assets...");

        $assets = $this->fileManager->getFiles($this->sourcePath);

        foreach ($assets as $asset) {
            $targetFile = $this->destinationPath . '/' . $asset->getRelativePathname();

            $this->fileManager->copy($asset->getPathname(), $targetFile);
            $this->output->file($asset->getPathname() . ' => ' . $targetFile);
        }
    }
}
