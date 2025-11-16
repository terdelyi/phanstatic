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
    public function itCanLoadConfigFile(): void
    {
        $configLoader = new ConfigLoader();
        $config = $configLoader->load(self::$dataPath);

        static::assertEquals(Config::DEFAULT_PATH, $config?->path);
        static::assertEquals('', $config?->title);
        static::assertEquals('http://localhost:8080', $config?->baseUrl);
    }

    #[Test]
    public function itCanLoadCustomConfig(): void
    {
        $customConfig = 'config/sample-config.php';
        $configLoader = new ConfigLoader();
        $config = $configLoader->load(self::$dataPath, $customConfig);

        static::assertEquals($customConfig, $config?->path);
        static::assertEquals('This is a custom config', $config?->title);
    }

    #[Test]
    public function itThrowsErrorOnInvalidCustomConfigFile(): void
    {
        $this->expectException(\Exception::class);

        (new ConfigLoader())->load(self::$dataPath, 'doesnotexist.php');
    }

    #[Test]
    public function itHandlesInvalidCustomConfig(): void
    {
        $this->expectExceptionMessage('Invalid config file content in');

        ob_start();

        try {
            (new ConfigLoader())->load(self::$dataPath, 'config/invalid-config.php');
        } finally {
            ob_get_clean();
        }
    }
}
