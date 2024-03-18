<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use stdClass;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Config\Site;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::getInstance([
            'site' => [
                'baseUrl' => 'https://example.com',
            ],
            'workingDirectory' => '../'
        ]);
    }

    /** @test */
    public function permalinkReturnsFullUrl()
    {
        $this->assertEquals('https://example.com/test-url', url('test-url'));
        $this->assertEquals('https://example.com/test-url-with-slash', url('/test-url-with-slash'));
    }

    /** @test */
    public function assetReturnsFullUrl()
    {
        $this->assertEquals('https://example.com/assets/image/test.jpg', asset('image/test.jpg'));
        $this->assertEquals('https://example.com/assets/css/test-with-slash.css', asset('/css/test-with-slash.css'));
    }
}
