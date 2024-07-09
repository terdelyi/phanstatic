<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Services;

use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\Models\RenderContext;

interface FileManagerInterface
{
    public function cleanDirectory(string $path): bool;

    public function exists(string $path): bool;

    /**
     * @param string|string[] $path
     */
    public function getFiles(array|string $path, ?string $pattern = null): Finder;

    /**
     * @param string|string[]     $path
     * @param int|string|string[] $level
     */
    public function getDirectories(array|string $path, array|int|string $level = ''): Finder;

    public function render(string $path, RenderContext $data): string;

    public function require(string $filePath, RenderContext $data): int;

    public function save(string $path, string $data): bool;

    public function copy(string $sourcePath, string $targetPath): void;
}
