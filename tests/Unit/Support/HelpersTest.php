<?php

declare(strict_types=1);

namespace Tests\Unit\New\Support;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Models\CollectionConfig;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\New\TestCase;

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
            'source-dir',
            'build-dir',
            'base-url',
            'title',
            ['meta' => 'value'],
            [new CollectionConfig('Test', 'test', 5)],
            ['generatorA', 'generatorB'],
        );
        $workingDir = 'test/working-dir';

        $this->helpers = new Helpers($config, $workingDir);
    }

    #[Test]
    public function itCanReturnBaseUrl(): void
    {
        $this->assertEquals('base-url', $this->helpers->getBaseUrl());
    }

    #[Test]
    public function itCanReturnBaseUrlWithCustomUrl(): void
    {
        $this->assertEquals('base-url/test', $this->helpers->getBaseUrl('test'));
    }

    #[Test]
    public function itCanReturnBaseUrlWhenCustomUrlHasLeadingSlash(): void
    {
        $this->assertEquals('base-url/test', $this->helpers->getBaseUrl('/test'));
    }

    #[Test]
    public function itCanReturnBuildDir(): void
    {
        $this->assertEquals('test/working-dir/build-dir', $this->helpers->getBuildDir());
    }

    #[Test]
    public function itCanReturnBuildDirWithCustomPath(): void
    {
        $this->assertEquals('test/working-dir/build-dir/custom-path', $this->helpers->getBuildDir('custom-path'));
    }

    #[Test]
    public function itCanReturnSourceDir(): void
    {
        $this->assertEquals('test/working-dir/source-dir', $this->helpers->getSourceDir());
    }

    #[Test]
    public function itCanReturnSourceDirWithCustomPath(): void
    {
        $this->assertEquals('test/working-dir/source-dir/custom-path', $this->helpers->getSourceDir('custom-path'));
    }

    #[Test]
    public function itReturnsAssetUrlWithoutLeadingSlash(): void
    {
        $this->assertEquals('base-url/assets/some-asset.jpg', $this->helpers->getAsset('some-asset.jpg'));
    }

    #[Test]
    public function itReturnsAssetUrlWithLeadingSlash(): void
    {
        $this->assertEquals('base-url/assets/some-asset.jpg', $this->helpers->getAsset('/some-asset.jpg'));
    }
}
