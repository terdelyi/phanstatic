<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Finder\SplFileInfo;

class File
{
    public static function fromPath(string $path, array $replace = []): SplFileInfo
    {
        $realPath = realpath($path);

        if ($realPath === false) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $realPath = str_replace('\\', '/', $realPath);
        $baseDir = dirname($realPath);

        $relativePath = str_replace($replace, [''], basename($baseDir));
        $relativePathname = ($relativePath ? $relativePath.'/' : '').basename($realPath);

        return new SplFileInfo($realPath, $relativePath, $relativePathname);
    }
}
