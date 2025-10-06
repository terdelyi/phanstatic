<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class AssetGenerator implements GeneratorInterface
{
    use OutputHelper;

    private string $sourcePath = 'assets';
    private string $destinationPath = 'assets';
    private Filesystem $filesystem;
    private Helpers $helpers;

    public function __construct(?Filesystem $filesystem = null, ?Helpers $helpers = null)
    {
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->helpers = $helpers ?? new Helpers();
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->setOutput($output);

        $this->text('Looking for assets...');

        if ( ! $this->filesystem->exists($this->getAssetsDir())) {
            $this->text('Skipping assets: %s directory doesn\'t exist', $this->getAssetsDir());

            return;
        }

        $assets = (new FileReader())->findFiles($this->getAssetsDir());
        foreach ($assets as $asset) {
            $this->process($asset);
        }

        $this->lines();
    }

    private function process(SplFileInfo $asset): void
    {
        $source = "{$this->sourcePath}/{$asset->getRelativePathname()}";
        $destination = "{$this->destinationPath}/{$asset->getRelativePathname()}";

        $this->filesystem->copy($asset->getPathname(), $this->helpers->getBuildDir($destination), true);

        $this->fromTo(
            $this->helpers->getSourceDir($source, true),
            $this->helpers->getBuildDir($destination, true)
        );
    }

    private function getAssetsDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }
}
