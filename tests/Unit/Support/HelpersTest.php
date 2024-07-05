<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\Config\ConfigBuilder;

/**
 * @internal
 */
class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $config = (new ConfigBuilder())
            ->setBaseUrl('https://example.com')
            ->build();

        \Mockery::mock('alias:Terdelyi\Phanstatic\Phanstatic')
            ->shouldReceive('getConfig')
            ->andReturn($config);
    }

    public function testPermalinkReturnsFullUrl(): void
    {
        $this->assertEquals('https://example.com/test-url', url('test-url'));
        $this->assertEquals('https://example.com/test-url-with-slash', url('/test-url-with-slash'));
    }

    public function testAssetReturnsFullUrl(): void
    {
        $this->assertEquals('https://example.com/assets/image/test.jpg', asset('image/test.jpg'));
        $this->assertEquals('https://example.com/assets/css/test-with-slash.css', asset('/css/test-with-slash.css'));
    }
}
