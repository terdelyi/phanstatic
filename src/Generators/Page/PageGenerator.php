<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators\Page;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Generators\GeneratorInterface;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class PageGenerator implements GeneratorInterface
{
    use OutputHelper;

    private string $sourcePath = 'pages';
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

        $this->text('Looking for pages...');

        if ( ! $this->filesystem->exists($this->getPagesDir())) {
            $this->text('Skipping pages: %s directory doesn\'t exist', $this->getPagesDir());

            return;
        }

        $pages = (new FileReader())->findFiles($this->getPagesDir(), '*.php');
        foreach ($pages as $page) {
            $this->process($page);
        }

        $this->lines();
    }

    private function process(SplFileInfo $file): void
    {
        $context = (new ContextBuilder())->build($file);
        $html = (new PhpCompiler())->render($file->getPathname(), $context);

        $this->filesystem->dumpFile($context->page->path, $html);

        $this->fromTo(
            $this->helpers->getSourceDir($context->page->relativePath, true),
            $this->helpers->getBuildDir($context->page->relativePath, true)
        );
    }

    private function getPagesDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }
}
