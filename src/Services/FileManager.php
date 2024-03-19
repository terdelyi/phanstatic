<?php

namespace Terdelyi\Phanstatic\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\Data\RenderData;
use Throwable;

use function dirname;

class FileManager
{
    private string $sourceFolder = 'content';
    private string $destinationFolder = 'dist';

    public function __construct(
        private Filesystem $filesystem,
        private Finder     $finder,
    ) {}

    public function getSourceFolder(string $path = ''): string
    {
        return $this->sourceFolder . $path;
    }

    public function getDestinationFolder(string $path = ''): string
    {
        return $this->destinationFolder . $path;
    }

    public function cleanDestinationFolder(): bool
    {
        $destinationFolder = $this->getDestinationFolder();

        if ($this->filesystem->exists($destinationFolder)) {
            $this->filesystem->remove($destinationFolder);

            return true;
        }

        return false;
    }

    public function exists($path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function getFiles(array|string $path, ?string $pattern = null): Finder
    {
        $files = $this->finder::create()
            ->files()
            ->in($path)
            ->notName('/^_/')
            ->notPath('/^_.*$/');

        if ($pattern !== null) {
            return $files->name($pattern);
        }

        return $files;
    }

    public function getDirectories(array|string $path, array|int|string $level = ''): Finder
    {
        return $this->finder::create()
            ->directories()
            ->in($path)
            ->notPath('/^_.*$/')
            ->depth($level);
    }

    public function render(string $path, RenderData $data): string
    {
        $obLevel = ob_get_level();

        ob_start();

        try {
            $this->require($path, $data);
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        }

        return ltrim(ob_get_clean());
    }

    public function require(string $filePath, RenderData $data): string
    {
        return (static function () use ($filePath, $data) {
            extract(get_object_vars($data), EXTR_SKIP);
            unset($data);

            return require $filePath;
        })();
    }

    public function save(string $path, string $data): bool
    {
        $outputDir = dirname($path);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0o755, true);
        }

        return file_put_contents($path, $data) !== false;
    }

    public function copy($sourcePath, $targetPath): void
    {
        $this->filesystem->copy($sourcePath, $targetPath, true);
    }
}
