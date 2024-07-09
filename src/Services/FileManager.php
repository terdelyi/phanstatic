<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Terdelyi\Phanstatic\Models\RenderContext;

class FileManager implements FileManagerInterface
{
    private Filesystem $filesystem;

    private Finder $finder;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
    }

    public function cleanDirectory(string $path): bool
    {
        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove($path);

            return true;
        }

        return false;
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    /**
     * @param string|string[] $path
     */
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

    /**
     * @param string|string[]     $path
     * @param int|string|string[] $level
     */
    public function getDirectories(array|string $path, array|int|string $level = ''): Finder
    {
        return $this->finder::create()
            ->directories()
            ->in($path)
            ->notPath('/^_.*$/')
            ->depth($level);
    }

    public function render(string $path, RenderContext $data): string
    {
        ob_start();

        set_error_handler($this->customErrorHandler());

        try {
            $this->require($path, $data);
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        $output = ob_get_clean();

        restore_error_handler();

        return $output !== false ? ltrim($output) : '';
    }

    public function require(string $filePath, RenderContext $data): int
    {
        return (static function () use ($filePath, $data) {
            $dataVars = get_object_vars($data);
            extract($dataVars, EXTR_SKIP);
            unset($data);

            return require $filePath;
        })();
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

    public function customErrorHandler(): \Closure
    {
        return function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }
}
