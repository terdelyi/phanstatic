<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Finder\SplFileInfo as SplFileInfoBase;

class SplFileInfo extends SplFileInfoBase
{
    public static function fromFilePath(string $filePath, string $replace = ''): self
    {
        $realPath = realpath($filePath);

        if ($realPath === false) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        $realPath = str_replace('\\', '/', $realPath);
        $baseDir = dirname($realPath);
        $relativePath = str_replace($replace, '', basename($baseDir));
        $relativePathname = basename($realPath);

        if ($relativePath) {
            $relativePathname = $relativePath.'/'.$relativePathname;
        }

        return new self($realPath, $relativePath, $relativePathname);
    }
}
