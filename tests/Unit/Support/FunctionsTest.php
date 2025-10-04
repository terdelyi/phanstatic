<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Phanstatic;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class FunctionsTest extends TestCase
{
    private Helpers&MockInterface $helperMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helperMock = m::mock(Helpers::class);
        $containerMock = m::mock('alias:'.Phanstatic::class);
        $containerMock->shouldReceive('get')
            ->set('helpers', $this->helperMock)
            ->andReturnSelf();
    }

    #[Test]
    public function permalinkReturnsFullUrl(): void
    {
        $permalink = 'test-permalink';
        $expectedUrl = 'http://example.com/test-permalink';

        $this->helperMock->shouldReceive('getBaseUrl')
            ->with($permalink)
            ->andReturn($expectedUrl);

        static::assertEquals($expectedUrl, url($permalink));
    }

    #[Test]
    public function assetReturnsFullUrl(): void
    {
        $file = 'image/test.jpg';
        $expectedUrl = 'https://example.com/assets/image/test.jpg';

        $this->helperMock->shouldReceive('getAsset')
            ->with($file)
            ->andReturn($expectedUrl);

        static::assertEquals($expectedUrl, asset($file));
    }
}
