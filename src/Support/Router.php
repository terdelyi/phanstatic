<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Generators\Page\ContextBuilder;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Readers\FileReader;

class Router
{
    private string $uri;

    private Helpers $helpers;

    public function __construct(?Helpers $helpers = null)
    {
        $this->helpers = $helpers ?? new Helpers();
    }

    public function handle(string $request): void
    {
        $this->uri = $this->parseRequest($request);

        if ($page = $this->getPage()) {
            $file = File::fromPath($page);
            $context = (new ContextBuilder())->build($file);
            $this->render($file->getPathname(), $context);
        }

        if ($collection = $this->getCollection()) {
            $file = File::fromPath($collection);
            $context = (new ContextBuilder())->build($file);
            $this->render($file->getPathname(), $context);
        }
    }

    public function parseRequest(string $request): string
    {
        $path = (string) parse_url($request, PHP_URL_PATH);
        $uri = trim($path, '/');

        return $uri === ''
            ? 'index'
            : htmlspecialchars($uri);
    }

    public function getPage(): ?string
    {
        $file = $this->helpers->getSourceDir('pages/'.$this->uri.'.php');

        return file_exists($file) ? $file : null;
    }

    public function getCollection(): ?string
    {
        $matchedCollection = null;

        foreach (Config::get()->collections as $key => $collection) {
            if (str_starts_with($this->uri, (string) $collection->slug) || str_starts_with($this->uri, $key)) {
                $matchedCollection = $collection;

                break;
            }
        }

        $files = (new FileReader())->findFiles(__DIR__.'/../../content/collections/posts', '*.md');
        foreach ($files as $file) {
            if ($this->uri === $matchedCollection->slug.'/'.$file->getFilenameWithoutExtension()) {
                dd('Single collection!');
            }
        }

        dd($matchedCollection);

        return '';
    }

    /**
     * @throws \Throwable
     */
    private function render(string $file, CompilerContext $context): void
    {
        echo (new PhpCompiler())->render($file, $context);

        exit;
    }
}
