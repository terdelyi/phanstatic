<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

use Symfony\Component\Filesystem\Filesystem;

class FileManager
{
    public function __construct(
        private Filesystem $filesystem
    ) {}

    public function cleanDirectory(string $path): bool
    {
        if (!$this->filesystem->exists($path)) {
            return false;
        }

        $this->filesystem->remove($path);

        return true;
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function save(string $path, string $data): bool
    {
        $outputDir = \dirname($path);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0o755, true);
        }

        return file_put_contents($path, $data) !== false;
    }

    public function copy(string $sourcePath, string $targetPath): void
    {
        $this->filesystem->copy($sourcePath, $targetPath, true);
    }
}
