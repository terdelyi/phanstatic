<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Models\CollectionConfig;
use Terdelyi\Phanstatic\Models\Config;
use Tests\Unit\TestCase;

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

        static::assertEquals('source-dir', $config->sourceDir);
        static::assertEquals('build-dir', $config->buildDir);
        static::assertEquals('base-url', $config->baseUrl);
        static::assertEquals('title', $config->title);
        static::assertEquals(['meta' => 'value'], $config->meta);
        static::assertEquals(['generatorA', 'generatorB'], $config->generators);
        static::assertEquals('Test', $config->collections[0]->title);
        static::assertEquals('test', $config->collections[0]->slug);
        static::assertEquals('5', $config->collections[0]->pageSize);
    }
}
