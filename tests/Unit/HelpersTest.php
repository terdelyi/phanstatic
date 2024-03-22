<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\Config\Config;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $config = Config::getInstance();
        $config->setSite([
            'baseUrl' => 'https://example.com',
        ]);
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
