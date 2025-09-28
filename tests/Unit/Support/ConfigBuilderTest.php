<?php

declare(strict_types=1);

namespace Tests\Unit\New\Support;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\ConfigBuilder;
use Tests\Unit\New\TestCase;

/**
 * @internal
 */
class ConfigBuilderTest extends TestCase
{
    #[Test]
    public function itCanCreateAConfigInstance(): void
    {
        $configBuilder = ConfigBuilder::make();

        $this->assertInstanceOf(ConfigBuilder::class, $configBuilder);
    }

    #[Test]
    public function itCanBuildConfig(): void
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
