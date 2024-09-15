<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Generators;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\New\Readers\FileReader;
use Terdelyi\Phanstatic\New\Support\FileManager;
use Terdelyi\Phanstatic\New\Support\Helpers;

class AssetGenerator implements GeneratorInterface
{
    private string $sourcePath = 'assets';
    private string $destinationPath = 'assets';

    public function __construct(
        private OutputInterface $output,
        private Helpers $helpers,
        private FileReader $fileReader,
        private FileManager $fileManager,
    ) {}

    public function run(): void
    {
        if (!$this->fileManager->exists($this->helpers->getSourceDir($this->sourcePath))) {
            $this->output->writeln(["Skipping assets: no 'content/assets' directory found", '']);

            return;
        }

        $this->output->writeln('Looking for assets...');

        $in = $this->helpers->getSourceDir($this->sourcePath);
        foreach ($this->fileReader->findFiles($in) as $asset) {
            $output = $this->process($asset);
            $this->output->writeln($output);
        }

        $this->output->writeln('');
    }

    private function process(SplFileInfo $asset): string
    {
        $this->fileManager->copy($asset->getPathname(), $this->helpers->getBuildDir("{$this->destinationPath}/{$asset->getRelativePathname()}"));

        $outputFrom = $this->helpers->getSourceDir("{$this->destinationPath}/{$asset->getRelativePathname()}", true);
        $outputTo = $this->helpers->getBuildDir("{$this->destinationPath}/{$asset->getRelativePathname()}", true);

        return $outputFrom.' => '.$outputTo;
    }
}
