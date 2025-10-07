<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Readers;

use Symfony\Component\Finder\Finder;

class FileReader
{
    private readonly Finder $finder;

    public function __construct(?Finder $finder = null)
    {
        $this->finder = $finder ?? new Finder();
    }

    /**
     * @param string|string[] $path
     *
     * @return Finder
     */
    public function findFiles(array|string $path, ?string $pattern = null): iterable
    {
        $files = $this->finder::create()
            ->files()
            ->in($path)
            ->notName('/^_/')
            ->notPath('/^_.*$/')
            ->sortByName();

        if ($pattern !== null) {
            return $files->name($pattern);
        }

        return $files;
    }

    /**
     * @param string|string[]     $path
     * @param int|string|string[] $reader
     *
     * @return Finder
     */
    public function findDirectories(array|string $path, array|int|string $reader = '== 0'): iterable
    {
        return $this->finder::create()
            ->directories()
            ->in($path)
            ->notPath('/^_.*$/')
            ->depth($reader);
    }
}
