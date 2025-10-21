<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators\Collection;

use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\Compilers\MarkdownCompiler;
use Terdelyi\Phanstatic\Models\Collection;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;
use Terdelyi\Phanstatic\Support\Helpers;

class SingleContextBuilder
{
    private Helpers $helpers;
    private Config $config;

    public function __construct(?Helpers $helpers = null, ?Config $config = null)
    {
        $this->helpers = $helpers ?? new Helpers();
        $this->config = $config ?? Config::get();
    }

    public function build(SplFileInfo $file, Collection $collection): CompilerContext
    {
        $page = $this->getPage($file, $collection);

        $site = new Site(
            title: $this->config->title,
            baseUrl: $this->config->baseUrl,
            meta: $this->config->meta
        );

        return new CompilerContext($site, $page);
    }

    public function getPage(SplFileInfo $file, Collection $collection): Page
    {
        $markdown = (new MarkdownCompiler())->render($file->getPathname());

        return $this->buildPage($file->getBasename('.md'), $collection->slug, $markdown);
    }

    private function buildPage(string $basename, string $slug, MarkdownCompiler $markdown): Page
    {
        $relativePath = $basename;
        if ($slug !== '') {
            $relativePath = $slug.'/'.$relativePath;
        }

        $meta = $markdown->meta();
        $title = ! isset($meta['title']) ? dd($markdown) : $meta['title'];
        unset($meta['title']);

        return new Page(
            path: $this->helpers->getBuildDir($relativePath.'/index.html'),
            relativePath: $relativePath,
            permalink: $relativePath.'/',
            url: url($relativePath.'/'),
            title: $title,
            content: $markdown->content(),
            meta: $meta,
        );
    }
}
