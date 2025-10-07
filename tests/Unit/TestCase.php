<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase as UnitTestCase;

/**
 * @internal
 */
class TestCase extends UnitTestCase
{
    use MockeryPHPUnitIntegration;

    protected static string $dataPath;
    protected bool $cleanUp = false;

    public static function setUpBeforeClass(): void
    {
        self::$dataPath = dirname(__DIR__).'/data';
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();

        if ($this->cleanUp) {
            $this->cleanDir(self::$dataPath.'/dist');
        }
    }

    private function cleanDir(string $dir): void
    {
        if ( ! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $path = $item->getPathname();

            if ($item->getFilename() === '.gitkeep') {
                continue;
            }

            if ($item->isDir()) {
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }
}
