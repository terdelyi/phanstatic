<?php

declare(strict_types=1);

namespace Tests\Unit\New\Support;

use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\ConfigLoader;

/**
 * @internal
 */
class ConfigLoaderTest extends TestCase
{
    public function testItCanLoadDefaultConfig(): void
    {
        $configLoader = new ConfigLoader();
        $config = $configLoader->load();

        $this->assertInstanceOf(Config::class, $config);
    }

    public function testItCanLoadCustomConfig(): void
    {
        $customConfig = './tests/data/config/sample-config.php';
        $configLoader = new ConfigLoader($customConfig);
        $config = $configLoader->load();

        $this->assertInstanceOf(Config::class, $config);
    }

    public function testInvalidCustomConfig(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectsOutput();

        $customConfig = './tests/data/config/invalid-config.php';
        $configLoader = new ConfigLoader($customConfig);

        ob_start();
        try {
            $configLoader->load();
        } finally {
            $output = ob_get_clean();
        }

        $this->assertEquals('This is an invalid config file.', $output);
    }

    public function testItThrowsErrorIfConfigDoesntExist(): void
    {
        $this->expectException(\RuntimeException::class);

        $customConfig = 'absolutely-random-file.php';
        $configLoader = new ConfigLoader($customConfig);
        $configLoader->load();
    }
}
