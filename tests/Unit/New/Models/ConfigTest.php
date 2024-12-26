<?php

declare(strict_types=1);

namespace Tests\Unit\New\Models;

use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\New\TestCase;
use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use Terdelyi\Phanstatic\New\Models\Config;

/**
 * @internal
 */
class ConfigTest extends TestCase
{
    #[Test]
    public function itCanCreateInstance(): void
    {
        $config = new Config(
            'source-dir',
            'build-dir',
            'base-url',
            'title',
            ['meta' => 'value'],
            [new CollectionConfig('Test', 'test', 5)],
            ['generatorA', 'generatorB'],
        );

        $this->assertEquals('source-dir', $config->sourceDir);
        $this->assertEquals('build-dir', $config->buildDir);
        $this->assertEquals('base-url', $config->baseUrl);
        $this->assertEquals('title', $config->title);
        $this->assertEquals(['meta' => 'value'], $config->meta);
        $this->assertEquals(['generatorA', 'generatorB'], $config->generators);
        $this->assertEquals('Test', $config->collections[0]->title);
        $this->assertEquals('test', $config->collections[0]->slug);
        $this->assertEquals('5', $config->collections[0]->pageSize);
    }
}
