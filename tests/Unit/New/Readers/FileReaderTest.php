<?php

declare(strict_types=1);

namespace Tests\Unit\New\Readers;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\New\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Terdelyi\Phanstatic\New\Readers\FileReader;

/**
 * @internal
 */
class FileReaderTest extends TestCase
{
    /** @var Finder|MockInterface */
    private Finder|MockInterface $finder;
    private FileReader $fileReader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = m::mock(Finder::class);
        $this->fileReader = new FileReader($this->finder);
    }

    #[Test]
    public function itReadsFiles(): void
    {
        $this->finder->expects('create')->andReturn($this->finder);
        $this->finder->expects('files')->andReturn($this->finder);
        $this->finder->expects('in')->andReturn($this->finder);
        $this->finder->expects('notName')->andReturn($this->finder);
        $this->finder->expects('NotPath')->andReturn($this->finder);

        $relativePath = 'tests/data/config/sample-config.php';
        $iteratorMock = new \ArrayIterator([
            new SplFileInfo(basename($relativePath), $relativePath, dirname($relativePath)),
        ]);

        $this->finder->expects('getIterator')->andReturn($iteratorMock);

        $files = $this->fileReader->findFiles('test-path');

        /** @var \SplFileInfo[] $iterator */
        $iterator = $files->getIterator();

        $this->assertCount(1, $iterator);
        $this->assertEquals('sample-config.php', $iterator[0]->getPathname());
    }
}
