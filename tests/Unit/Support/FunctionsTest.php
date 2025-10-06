<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Mockery as m;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\PreserveGlobalState as PreserveGlobalStateAlias;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\TestCase;

/**
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalStateAlias(false)]
class FunctionsTest extends TestCase
{
    private (Helpers&MockInterface)|(LegacyMockInterface&MockInterface) $helperMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helperMock = m::mock('overload:'.Helpers::class);
    }

    #[Test]
    public function permalinkReturnsFullUrl(): void
    {
        $permalink = 'test-permalink';
        $expectedUrl = Config::DEFAULT_URL.'/test-permalink';

        $this->helperMock->shouldReceive('getBaseUrl')
            ->once()
            ->with($permalink)
            ->andReturn($expectedUrl);

        static::assertEquals($expectedUrl, url($permalink));
    }

    #[Test]
    public function assetReturnsFullUrl(): void
    {
        $file = 'image/test.jpg';
        $expectedUrl = Config::DEFAULT_URL.'/assets/image/test.jpg';

        $this->helperMock->shouldReceive('getAsset')
            ->once()
            ->with($file)
            ->andReturn($expectedUrl);

        static::assertEquals($expectedUrl, asset($file));
    }

    #[Test]
    public function sourceDirReturnsFullUrl(): void
    {
        $file = 'test.php';
        $expectedPath = Config::DEFAULT_SOURCE.'/test.php';

        $this->helperMock->shouldReceive('getSourceDir')
            ->once()
            ->with($file, true)
            ->andReturn($expectedPath);

        static::assertEquals($expectedPath, source_dir($file, true));
    }

    #[Test]
    public function buildDirReturnsFullUrl(): void
    {
        $file = 'test.php';
        $expectedPath = Config::DEFAULT_BUILD.'/test.php';

        $this->helperMock->shouldReceive('getBuildDir')
            ->once()
            ->with($file, true)
            ->andReturn($expectedPath);

        static::assertEquals($expectedPath, build_dir($file, true));
    }
}
