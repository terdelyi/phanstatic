<?php

namespace Terdelyi\Phanstatic\ContentBuilders\Page;

use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\ContentBuilders\BuilderContextInterface;
use Terdelyi\Phanstatic\ContentBuilders\BuilderInterface;
use Terdelyi\Phanstatic\ContentBuilders\RenderContext;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\SiteConfig;
use Terdelyi\Phanstatic\Services\FileManager;
use Terdelyi\Phanstatic\Support\Output\OutputInterface;
use Throwable;

class PageBuilder implements BuilderInterface
{
    private string $sourcePath = '/pages';
    private Config $config;
    private OutputInterface $output;

    /**
     * @throws Throwable
     */
    public function build(BuilderContextInterface $context): void
    {
        $this->config = $context->getConfig();
        $this->output = $context->getOutput();

        $fileManager = new FileManager();

        if (!$fileManager->exists($this->getSourcePath())) {
            $this->output->action("Skipping pages: no 'content/pages' directory found");

            return;
        }

        $this->output->action("Looking for pages...");

        $pages = $fileManager->getFiles($this->getSourcePath(), '*.php');

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

            $html = $fileManager->render($page->getPathname(), $data);

            if ($fileManager->save($fileData['path'], $html) !== false) {
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
        $permalink = ($basename === 'index') ? '/' : ($file->getRelativePath() !== "" ? "/{$file->getRelativePath()}/$basename/" : "/$basename/");
        $targetPath = $permalink === '/' ? $this->config->getBuildDir('/index.html') : $this->config->getBuildDir("{$permalink}index.html");

        return [
            'path' => $targetPath,
            'permalink' => $permalink,
        ];
    }

    private function getSourcePath(): string
    {
        return $this->config->getSourceDir($this->sourcePath);
    }
}
