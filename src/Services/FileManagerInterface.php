<?php

namespace Terdelyi\Phanstatic\Services;

use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\ContentBuilders\RenderContext;

interface FileManagerInterface
{
    public function cleanFolder(string $path): bool;

    public function exists(string $path): bool;

    /**
     * @param string|string[] $path
     */
    public function getFiles(array|string $path, ?string $pattern = null): Finder;

    /**
     * @param string|string[] $path
     * @param string|string[]|int $level
     */
    public function getDirectories(array|string $path, array|int|string $level = ''): Finder;

    public function render(string $path, RenderContext $data): string;

    public function require(string $filePath, RenderContext $data): string;

    public function save(string $path, string $data): bool;

    public function copy(string $sourcePath, string $targetPath): void;
}
