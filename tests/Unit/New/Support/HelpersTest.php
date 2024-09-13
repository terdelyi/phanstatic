<?php

declare(strict_types=1);

namespace Tests\Unit\New\Support;

use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\Helpers;

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

    public function testItCanReturnBaseUrl(): void
    {
        $this->assertEquals('base-url', $this->helpers->getBaseUrl());
    }

    public function testItCanReturnBaseUrlWithCustomUrl(): void
    {
        $this->assertEquals('base-url/test', $this->helpers->getBaseUrl('test'));
    }

    public function testItCanReturnBaseUrlWithCustomUrlStartsWithSlash(): void
    {
        $this->assertEquals('base-url/test', $this->helpers->getBaseUrl('/test'));
    }

    public function testItCanReturnBuildDir(): void
    {
        $this->assertEquals('test/working-dir/build-dir', $this->helpers->getBuildDir());
    }

    public function testItCanReturnBuildDirWithCustomPath(): void
    {
        $this->assertEquals('test/working-dir/build-dir/custom-path', $this->helpers->getBuildDir('custom-path'));
    }

    public function testItCanReturnSourceDir(): void
    {
        $this->assertEquals('test/working-dir/source-dir', $this->helpers->getSourceDir());
    }

    public function testItCanReturnSourceDirWithCustomPath(): void
    {
        $this->assertEquals('test/working-dir/source-dir/custom-path', $this->helpers->getSourceDir('custom-path'));
    }
}
