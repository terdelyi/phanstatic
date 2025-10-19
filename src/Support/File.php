<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Finder\SplFileInfo;

class File
{
    public static function fromPath(string $path): SplFileInfo
    {
        $realPath = realpath($path);

        if ($realPath === false) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $realPath = str_replace('\\', '/', $realPath);
        $baseDir = dirname($realPath);

        $relativePath = basename($baseDir);
        $relativePathname = $relativePath.'/'.basename($realPath);

        return new SplFileInfo($realPath, $relativePath, $relativePathname);
    }
}
