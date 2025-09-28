<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Mockery as m;
use Mockery\MockInterface;
use Terdelyi\Phanstatic\Phanstatic;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class FunctionsTest extends TestCase
{
    private Helpers|MockInterface $helperMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helperMock = m::mock('alias:'.Helpers::class);
        $containerMock = m::mock('alias:'.Phanstatic::class);
        $containerMock->shouldReceive('getContainer')
            ->andReturn($containerMock);
        $containerMock->shouldReceive('get')
            ->with(Helpers::class)
            ->andReturn($this->helperMock);
    }

    public function testPermalinkReturnsFullUrl(): void
    {
        $permalink = 'test-permalink';
        $expectedUrl = 'http://example.com/test-permalink';

        $this->helperMock->shouldReceive('getBaseUrl')
            ->with($permalink)
            ->andReturn($expectedUrl);

        $this->assertEquals($expectedUrl, url($permalink));
    }

    public function testAssetReturnsFullUrl(): void
    {
        $file = 'image/test.jpg';
        $expectedUrl = 'https://example.com/assets/image/test.jpg';

        $this->helperMock->shouldReceive('getAsset')
            ->with($file)
            ->andReturn($expectedUrl);

        $this->assertEquals($expectedUrl, asset($file));
    }
}
