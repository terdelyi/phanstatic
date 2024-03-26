<?php

namespace Terdelyi\Phanstatic\Builders\Page;

use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Builders\BuilderInterface;
use Terdelyi\Phanstatic\Builders\RenderContext;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\SiteConfig;
use Terdelyi\Phanstatic\Console\Output\BuildOutputInterface;
use Terdelyi\Phanstatic\Services\FileManager;
use Throwable;

class PageBuilder implements BuilderInterface
{
    private string $sourcePath = '/pages';

    public function __construct(
        private readonly FileManager          $fileManager,
        private readonly BuildOutputInterface $output,
        private readonly Config               $config,
    ) {
        $this->sourcePath = $this->config->getSourceDir($this->sourcePath);
    }

    /**
     * @throws Throwable
     */
    public function build(): void
    {
        if (!$this->fileManager->exists($this->sourcePath)) {
            $this->output->action("Skipping pages: no 'content/pages' directory found");

            return;
        }

        $this->output->action("Looking for pages...");

        $pages = $this->fileManager->getFiles($this->sourcePath, '*.php');

        foreach ($pages as $page) {
            $fileData = $this->getFileData($page);
            $data = new RenderContext(
                site: new SiteConfig(
                    title: $this->config->getTitle(),
                    baseUrl: $this->config->getBaseUrl(),
                    meta: $this->config->getMeta()
                ),
                page: new Page(
                    path: $fileData['path'],
                    permalink: $fileData['permalink'],
                    url: url($fileData['permalink'])
                )
            );

            $html = $this->fileManager->render($page->getPathname(), $data);

            if ($this->fileManager->save($fileData['path'], $html) !== false) {
                $this->output->file($page->getPathname() . ' => ' . $fileData['path']);
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function getFileData(SplFileInfo $file): array
    {
        $basename = $file->getBasename('.php');
        $permalink = ($basename === 'index') ? '/' : ($file->getRelativePath() !== "" ? "/{$file->getRelativePath()}/{$basename}/" : "/{$basename}/");
        $targetPath = $permalink === '/' ? $this->config->getBuildDir('/index.html') : $this->config->getBuildDir("{$permalink}index.html");

        return [
            'path' => $targetPath,
            'permalink' => $permalink,
        ];
    }
}
