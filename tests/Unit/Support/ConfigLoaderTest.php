<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\ConfigLoader;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class ConfigLoaderTest extends TestCase
{
    #[Test]
    public function itCanLoadDefaultConfig(): void
    {
        $configLoader = new ConfigLoader();
        $config = $configLoader->load();

        static::assertEquals(Config::DEFAULT_PATH, $config?->path);
        static::assertEquals('http://localhost:8080', $config?->baseUrl);
    }

    #[Test]
    public function itCanLoadCustomConfig(): void
    {
        $customConfig = self::$dataPath.'/config/sample-config.php';
        $configLoader = new ConfigLoader();
        $config = $configLoader->load($customConfig);

        static::assertEquals($customConfig, $config?->path);
        static::assertEquals('This is a custom config', $config?->title);
    }

    #[Test]
    public function itHandlesInvalidCustomConfig(): void
    {
        $this->expectsOutput();

        $customConfig = self::$dataPath.'/config/invalid-config.php';
        $configLoader = new ConfigLoader();

        ob_start();

        try {
            $configLoader->load($customConfig);
        } finally {
            $output = ob_get_clean() ?: '';
        }

        static::assertStringStartsWith('This is an invalid config file.Invalid config file content. Please return a Config object in', $output);
    }
}
