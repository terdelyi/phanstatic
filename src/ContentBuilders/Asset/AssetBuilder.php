<?php

namespace Terdelyi\Phanstatic\ContentBuilders\Asset;

use Terdelyi\Phanstatic\ContentBuilders\BuilderContextInterface;
use Terdelyi\Phanstatic\ContentBuilders\BuilderInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\FileManagerInterface;
use Terdelyi\Phanstatic\Support\OutputInterface;

class AssetBuilder implements BuilderInterface
{
    private string $sourcePath = 'assets';
    private string $destinationPath = 'assets';
    private Config $config;
    private OutputInterface $output;
    private FileManagerInterface $fileManager;

    public function __construct(BuilderContextInterface $context)
    {
        $this->config = $context->getConfig();
        $this->output = $context->getOutput();
        $this->fileManager = $context->getFileManager();
    }

    public function build(): void
    {
        if (!$this->fileManager->exists($this->getSourcePath())) {
            $this->output->action("Skipping assets: no 'content/assets' directory found");

            return;
        }

        $this->output->action("Looking for assets...");

        $assets = $this->fileManager->getFiles($this->getSourcePath());

        foreach ($assets as $asset) {
            $this->fileManager->copy($asset->getPathname(), $this->getDestinationPath($asset->getRelativePathname()));

            $outputFrom = $this->getSourcePath($asset->getRelativePathname(), true);
            $outputTo = $this->getDestinationPath($asset->getRelativePathname(), true);
            $this->output->file($outputFrom . ' => ' . $outputTo);
        }

        $this->output->space();
    }

    private function getSourcePath(?string $path = null, bool $relative = false): string
    {
        $sourcePath = $path !== null ? $this->sourcePath . '/' . $path : $this->sourcePath;

        return $this->config->getSourceDir($sourcePath, $relative);
    }

    private function getDestinationPath(?string $path = null, bool $relative = false): string
    {
        $destinationPath = $path !== null ? $this->destinationPath . '/' . $path : $this->sourcePath;

        return $this->config->getBuildDir($destinationPath, $relative);
    }
}
