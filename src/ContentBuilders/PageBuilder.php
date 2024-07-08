<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Models\BuilderContextInterface;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\RenderContext;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Services\FileManagerInterface;
use Terdelyi\Phanstatic\Support\OutputInterface;

class PageBuilder implements BuilderInterface
{
    private string $sourcePath = 'pages';
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
            $this->output->action("Skipping pages: no 'content/pages' directory found");

            return;
        }

        $this->output->action('Looking for pages...');

        foreach ($this->getPages() as $page) {
            $output = $this->process($page);

            if ($output !== null) {
                $this->output->file($output);
            }
        }

        $this->output->space();
    }

    private function getPages(): Finder
    {
        return $this->fileManager->getFiles($this->getSourcePath(), '*.php');
    }

    private function process(SplFileInfo $page): ?string
    {
        $fileData = $this->getFileData($page);

        $data = new RenderContext(
            site: new Site(
                title: $this->config->getTitle(),
                baseUrl: $this->config->getBaseUrl(),
                meta: $this->config->getMeta()
            ),
            page: new Page(
                path: $fileData['path'],
                relativePath: $fileData['relativePath'],
                permalink: $fileData['permalink'],
                url: url($fileData['permalink'])
            )
        );

        $html = $this->fileManager->render($page->getPathname(), $data);
        $file = $this->fileManager->save($fileData['path'], $html);

        if (!$file) {
            return null;
        }

        $outputFrom = $this->getSourcePath($page->getRelativePathname(), true);
        $outputTo = $this->config->getBuildDir($fileData['relativePath'], true);

        return $outputFrom.' => '.$outputTo;
    }

    /**
     * @return array<string, string>
     */
    private function getFileData(SplFileInfo $file): array
    {
        $basename = $file->getBasename('.php');
        $permalink = ($basename === 'index') ? '/' : ($file->getRelativePath() !== '' ? "/{$file->getRelativePath()}/{$basename}/" : "/{$basename}/");
        $permalinkWithoutSlash = substr($permalink, 1);
        $relativePath = $permalink === '/' ? 'index.html' : "{$permalinkWithoutSlash}index.html";
        $path = $this->config->getBuildDir($relativePath);

        return [
            'basename' => $basename,
            'path' => $path,
            'relativePath' => $relativePath,
            'permalink' => $permalink,
        ];
    }

    private function getSourcePath(?string $path = null, bool $relative = false): string
    {
        $sourcePath = $path !== null ? $this->sourcePath.'/'.$path : $this->sourcePath;

        return $this->config->getSourceDir($sourcePath, $relative);
    }
}
