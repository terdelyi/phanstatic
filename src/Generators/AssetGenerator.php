<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;

class AssetGenerator implements GeneratorInterface
{
    private string $sourcePath = 'assets';
    private string $destinationPath = 'assets';

    public function __construct(
        private OutputInterface $output,
        private Helpers $helpers,
        private FileReader $fileReader,
        private Filesystem $filesystem,
    ) {}

    public function run(): void
    {
        if (!$this->filesystem->exists($this->helpers->getSourceDir($this->sourcePath))) {
            $this->output->writeln(["Skipping assets: no 'content/assets' directory found", '']);

            return;
        }

        $this->output->writeln('Looking for assets...');

        $in = $this->helpers->getSourceDir($this->sourcePath);
        foreach ($this->fileReader->findFiles($in) as $asset) {
            var_dump($asset);

            exit;
            $output = $this->process($asset);
            $this->output->writeln($output);
        }

        $this->output->writeln('');
    }

    private function process(SplFileInfo $asset): string
    {
        $source = "{$this->sourcePath}/{$asset->getRelativePathname()}";
        $destination = "{$this->destinationPath}/{$asset->getRelativePathname()}";

        $this->filesystem->copy($asset->getPathname(), $this->helpers->getBuildDir($destination), true);

        $outputFrom = $this->helpers->getSourceDir($source, true);
        $outputTo = $this->helpers->getBuildDir($destination, true);

        return $outputFrom.' => '.$outputTo;
    }
}
