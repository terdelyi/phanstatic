<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Phanstatic;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class AssetGenerator implements GeneratorInterface
{
    use OutputHelper;

    private string $sourcePath = 'assets';
    private string $destinationPath = 'assets';
    private FileReader $fileReader;
    private Filesystem $filesystem;
    private Helpers $helpers;

    public function __construct()
    {
        $this->fileReader = new FileReader(new Finder());
        $this->filesystem = new Filesystem();
        $this->helpers = Phanstatic::get()->helpers;
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        if ( ! $this->filesystem->exists($this->getAssetsDir())) {
            $output->writeln(sprintf('Skipping assets: %s doesn\'t exist', $this->getAssetsDir()));

            return;
        }

        $output->writeln('Looking for assets...');

        foreach ($this->fileReader->findFiles($this->getAssetsDir()) as $asset) {
            $log = $this->process($asset);
            $output->writeln($log);
        }

        $this->lines();
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

    private function getAssetsDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }
}
