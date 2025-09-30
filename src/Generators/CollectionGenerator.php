<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Phanstatic;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class CollectionGenerator implements GeneratorInterface
{
    use OutputHelper;

    private string $sourcePath = 'collections';
    private Filesystem $filesystem;
    private Helpers $helpers;
    private Config $config;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->helpers = Phanstatic::get()->helpers;
        $this->config = Phanstatic::get()->config;
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->setOutput($output);

        $this->text('Looking for collections...');

        if ( ! $this->filesystem->exists($this->getCollectionsDir())) {
            $this->text('Skipping pages: %s directory doesn\'t exist', $this->getCollectionsDir());

            return;
        }

        $collections = (new FileReader())->findDirectories($this->getCollectionsDir());
        foreach ($collections as $collection) {
            $this->process($collection);
        }

        $this->lines();
    }

    private function process(SplFileInfo $directory): void
    {
        $collection = $this->parseCollection($directory);
        $this->text("Collection '%s' found. Looking for items...", $collection->title);
    }

    private function getCollectionsDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }

    /**
     * @throws \Exception
     */
    private function parseCollection(SplFileInfo $directory): Collection
    {
        $collection = $directory->getBasename();
        $config = $this->config->collections[$collection] ?? null;
        $title = $config->title ?? ucwords($directory->getBasename());

        if ( ! $config) {
            throw new \Exception(sprintf("Configuration for collection '%s' is missing", $title));
        }

        $singleTemplate = $directory->getPathname().'/single.php';
        $indexTemplate = $directory->getPathname().'/index.php';

        if ( ! $this->filesystem->exists($singleTemplate)) {
            throw new \Exception(sprintf("Collection '%s' must have a single template at %s", $title, $singleTemplate));
        }

        return new Collection(
            title: $title,
            basename: $directory->getBasename(),
            sourceDir: $directory->getPathname(),
            slug: $config->slug ?? $directory->getBasename(),
            singleTemplate: $singleTemplate,
            indexTemplate: $indexTemplate,
            items: [],
            pageSize: $config->pageSize ?? 10
        );
    }
}
