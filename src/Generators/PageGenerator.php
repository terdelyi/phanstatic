<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Readers\FileReader;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class PageGenerator implements GeneratorInterface
{
    use OutputHelper;

    private string $sourcePath = 'pages';
    private Filesystem $filesystem;
    private Helpers $helpers;
    private Config $config;

    public function __construct(?Filesystem $filesystem = null, ?Helpers $helpers = null, ?Config $config = null)
    {
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->helpers = $helpers ?? new Helpers();
        $this->config = $config ?? Config::get();
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
        $fileData = $this->getFileData($file);
        $context = $this->buildContext($fileData);
        $html = (new PhpCompiler())->render($file->getPathname(), $context);

        $this->filesystem->dumpFile($fileData['path'], $html);

        $this->fromTo(
            $this->helpers->getSourceDir($fileData['relativePath'], true),
            $this->helpers->getBuildDir($fileData['relativePath'], true)
        );
    }

    /**
     * @return array<string, string>
     */
    private function getFileData(SplFileInfo $file): array
    {
        $permalink = $this->createPermalink($file);
        $relativePath = $this->createRelativePathFromPermalink($permalink);

        return [
            // 'basename' => $file->getBasename('.php'),
            'path' => $this->helpers->getBuildDir($relativePath),
            'relativePath' => $relativePath,
            'permalink' => $permalink,
        ];
    }

    private function createPermalink(SplFileInfo $file): string
    {
        $basename = $file->getBasename('.php');

        if ($basename === 'index') {
            return '/';
        }

        if ($file->getRelativePath() !== '') {
            return "/{$file->getRelativePath()}/{$basename}/";
        }

        return "/{$basename}/";
    }

    private function createRelativePathFromPermalink(string $permalink): string
    {
        $permalinkWithoutSlash = substr($permalink, 1);

        return $permalink === '/' ? 'index.html' : "{$permalinkWithoutSlash}index.html";
    }

    private function getPagesDir(): string
    {
        return $this->helpers->getSourceDir($this->sourcePath);
    }

    /** @param array<string,string> $fileData */
    private function buildContext(array $fileData): CompilerContext
    {
        $site = new Site(
            title: $this->config->title,
            baseUrl: $this->config->baseUrl,
            meta: $this->config->meta
        );
        $page = new Page(
            path: $fileData['path'],
            relativePath: $fileData['relativePath'],
            permalink: $fileData['permalink'],
            url: $this->helpers->getBaseUrl($fileData['permalink'])
        );

        return new CompilerContext($site, $page);
    }
}
