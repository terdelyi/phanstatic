<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\Filesystem\Filesystem;

// TODO: Just replace it with FileManager
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

    public function save(string $path, string $data): void
    {
        $this->filesystem->dumpFile($path, $data);
    }

    public function copy(string $sourcePath, string $targetPath): void
    {
        $this->filesystem->copy($sourcePath, $targetPath, true);
    }
}
