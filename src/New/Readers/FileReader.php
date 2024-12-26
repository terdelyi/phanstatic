<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Readers;

use Symfony\Component\Finder\Finder;

class FileReader
{
    public function __construct(
        private Finder $finder
    ) {}

    /**
     * @param string|string[] $path
     * @return Finder
     */
    public function findFiles(array|string $path, ?string $pattern = null): iterable
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
     * @param int|string|string[] $reader
     * @return Finder
     */
    public function findDirectories(array|string $path, array|int|string $reader = ''): iterable
    {
        return $this->finder::create()
            ->directories()
            ->in($path)
            ->notPath('/^_.*$/')
            ->depth($reader);
    }
}
