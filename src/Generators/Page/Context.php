<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators\Page;

use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Support\Helpers;

class Context
{
    private Helpers $helpers;
    private Config $config;

    public function __construct(?Helpers $helpers = null, ?Config $config = null)
    {
        $this->helpers = $helpers ?? new Helpers();
        $this->config = $config ?? Config::get();
    }

    /**
     * @return array<string, string>
     */
    public function getFileData(SplFileInfo $file): array
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

    public function buildContext(SplFileInfo $file): CompilerContext
    {
        $fileData = (new Context())->getFileData($file);

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
}
