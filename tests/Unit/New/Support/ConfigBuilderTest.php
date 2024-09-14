<?php

declare(strict_types=1);

namespace Tests\Unit\New\Support;

use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\ConfigBuilder;

/**
 * @internal
 */
class ConfigBuilderTest extends TestCase
{
    public function testItCanCreateAConfigInstance(): void
    {
        $configBuilder = ConfigBuilder::make();

        $this->assertInstanceOf(ConfigBuilder::class, $configBuilder);
    }

    public function testItCanBuildConfig(): void
    {
        $configBuilder = ConfigBuilder::make();
        $configBuilder->setSourceDir('/test/source-dir');
        $configBuilder->setBuildDir('/test/build-dir');
        $configBuilder->setTitle('Test title');
        $configBuilder->setBaseUrl('http://localhost');
        $configBuilder->setMeta(['meta' => 'value']);
        $configBuilder->setGenerators(['generatorA', 'generatorB']);
        $config = $configBuilder->build();

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('Test title', $config->title);
    }
}
