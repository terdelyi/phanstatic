<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Models\CollectionConfig;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class HelpersTest extends TestCase
{
    private Helpers $helpers;

    protected function setUp(): void
    {
        parent::setUp();

        $config = new Config(
            workingDir: 'test/working-dir',
            sourceDir: 'source-dir',
            buildDir: 'build-dir',
            baseUrl: 'base-url',
            title: 'title',
            meta: ['meta' => 'value'],
            collections: [
                'test' => new CollectionConfig('Test', 'test', 5),
            ],
            generators: ['generatorA', 'generatorB'],
        );

        $this->helpers = new Helpers($config);
    }

    #[Test]
    public function itCanReturnBaseUrl(): void
    {
        static::assertEquals('base-url', $this->helpers->getBaseUrl());
    }

    #[Test]
    public function itCanReturnBaseUrlWithCustomUrl(): void
    {
        static::assertEquals('base-url/test', $this->helpers->getBaseUrl('test'));
    }

    #[Test]
    public function itCanReturnBaseUrlWhenCustomUrlHasLeadingSlash(): void
    {
        static::assertEquals('base-url/test', $this->helpers->getBaseUrl('/test'));
    }

    #[Test]
    public function itCanReturnBuildDir(): void
    {
        static::assertEquals('test/working-dir/build-dir', $this->helpers->getBuildDir());
    }

    #[Test]
    public function itCanReturnBuildDirWithCustomPath(): void
    {
        static::assertEquals('test/working-dir/build-dir/custom-path', $this->helpers->getBuildDir('custom-path'));
    }

    #[Test]
    public function itCanReturnSourceDir(): void
    {
        static::assertEquals('test/working-dir/source-dir', $this->helpers->getSourceDir());
    }

    #[Test]
    public function itCanReturnSourceDirWithCustomPath(): void
    {
        static::assertEquals('test/working-dir/source-dir/custom-path', $this->helpers->getSourceDir('custom-path'));
    }

    #[Test]
    public function itReturnsAssetUrlWithoutLeadingSlash(): void
    {
        static::assertEquals('base-url/assets/some-asset.jpg', $this->helpers->getAsset('some-asset.jpg'));
    }

    #[Test]
    public function itReturnsAssetUrlWithLeadingSlash(): void
    {
        static::assertEquals('base-url/assets/some-asset.jpg', $this->helpers->getAsset('/some-asset.jpg'));
    }
}
