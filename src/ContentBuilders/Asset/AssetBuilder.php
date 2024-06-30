<?php

namespace Terdelyi\Phanstatic\ContentBuilders\Asset;

use Terdelyi\Phanstatic\ContentBuilders\BuilderContextInterface;
use Terdelyi\Phanstatic\ContentBuilders\BuilderInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\FileManager;
use Terdelyi\Phanstatic\Support\Output\OutputInterface;

class AssetBuilder implements BuilderInterface
{
    private string $sourcePath = '/assets';
    private string $destinationPath = '/assets';
    private Config $config;
    private OutputInterface $output;

    public function build(BuilderContextInterface $context): void
    {
        $this->config = $context->getConfig();
        $this->output = $context->getOutput();

        $fileManager = new FileManager();

        if (!$fileManager->exists($this->getSourcePath())) {
            $this->output->action("Skipping assets: no 'content/assets' directory found");

            return;
        }

        $this->output->action("Looking for assets...");

        $assets = $fileManager->getFiles($this->getSourcePath());

        foreach ($assets as $asset) {
            $targetFile = $this->getDestinationPath() . '/' . $asset->getRelativePathname();

            $fileManager->copy($asset->getPathname(), $targetFile);
            $this->output->file($asset->getPathname() . ' => ' . $targetFile);
        }
    }

    private function getSourcePath(): string
    {
        return $this->config->getSourceDir($this->sourcePath);
    }

    private function getDestinationPath(): string
    {
        return $this->config->getBuildDir($this->destinationPath);
    }
}
