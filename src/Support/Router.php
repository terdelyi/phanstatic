<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Generators\Page\Context;
use Terdelyi\Phanstatic\Models\CompilerContext;

class Router
{
    private string $uri;

    private Helpers $helpers;

    private function __construct(?Helpers $helpers = null)
    {
        $this->helpers = $helpers ?? new Helpers();
    }

    public function handle(string $request): void
    {
        $this->uri = $this->parseRequest($request);

        if ($page = $this->getPage()) {
            $file = File::fromPath($page);
            $context = (new Context())->buildContext($file);
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
        throw new \Exception('Not implemented');
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
