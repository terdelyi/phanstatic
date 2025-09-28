<?php

declare(strict_types=1);

namespace Tests\Unit\New\Support;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\New\Support\ConfigLoader;
use Tests\Unit\New\TestCase;

/**
 * @internal
 */
class ConfigLoaderTest extends TestCase
{
    #[Test]
    public function itCanLoadDefaultConfig(): void
    {
        $configLoader = new ConfigLoader('');
        $config = $configLoader->load();

        $this->assertEquals(null, $config->path);
        $this->assertEquals('http://localhost:8000', $config->baseUrl);
    }

    #[Test]
    public function itCanLoadCustomConfig(): void
    {
        $customConfig = './tests/data/config/sample-config.php';
        $configLoader = new ConfigLoader($customConfig);
        $config = $configLoader->load();

        $this->assertEquals($customConfig, $config->path);
        $this->assertEquals('This is a custom config', $config->title);
    }

    #[Test]
    public function itHandlesInvalidCustomConfig(): void
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

    #[Test]
    public function itThrowsErrorIfConfigDoesntExist(): void
    {
        $this->expectException(\RuntimeException::class);

        $customConfig = 'absolutely-random-file.php';
        $configLoader = new ConfigLoader($customConfig);
        $configLoader->load();
    }
}
