<?php

namespace Terdelyi\Phanstatic\Builders\Page;

use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Builders\BuilderInterface;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Data\RenderData;
use Terdelyi\Phanstatic\Data\Page;
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
    )
    {
        $this->sourcePath = $this->fileManager->getSourceFolder($this->sourcePath);;
    }

    /**
     * @throws Throwable
     */
    public function build(): void
    {
        $this->output->action("Looking for pages...");

        $pages = $this->fileManager->getFiles($this->sourcePath, '*.php');

        foreach ($pages as $page) {
            $fileData = $this->getFileData($page);
            $data = new RenderData(
                site: $this->config->getSite(),
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

    private function getFileData(SplFileInfo $file): array
    {
        $basename = $file->getBasename('.php');
        $permalink = ($file->getRelativePath() !== "") ? "/{$file->getRelativePath()}/{$basename}/" : "/{$basename}/";

        if ($basename === 'index') {
            $permalink = '/';
        }

        $targetPath = $this->fileManager->getDestinationFolder("{$permalink}index.html");

        if ($permalink === '/') {
            $targetPath = $this->fileManager->getDestinationFolder('/index.html');
        }

        return [
            'path' => $targetPath,
            'permalink' => $permalink,
        ];
    }
}