<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Models\BuilderContextInterface;
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
            $this->output->space();

            return;
        }

        $this->output->action('Looking for assets...');

        foreach ($this->getAssets() as $asset) {
            $output = $this->process($asset);
            $this->output->file($output);
        }

        $this->output->space();
    }

    private function getAssets(): Finder
    {
        return $this->fileManager->getFiles($this->getSourcePath());
    }

    private function process(SplFileInfo $asset): string
    {
        $this->fileManager->copy($asset->getPathname(), $this->getDestinationPath($asset->getRelativePathname()));

        $outputFrom = $this->getSourcePath($asset->getRelativePathname(), true);
        $outputTo = $this->getDestinationPath($asset->getRelativePathname(), true);

        return $outputFrom.' => '.$outputTo;
    }

    private function getSourcePath(?string $path = null, bool $relative = false): string
    {
        $sourcePath = $path !== null ? $this->sourcePath.'/'.$path : $this->sourcePath;

        return $this->config->getSourceDir($sourcePath, $relative);
    }

    private function getDestinationPath(?string $path = null, bool $relative = false): string
    {
        $destinationPath = $path !== null ? $this->destinationPath.'/'.$path : $this->sourcePath;

        return $this->config->getBuildDir($destinationPath, $relative);
    }
}
