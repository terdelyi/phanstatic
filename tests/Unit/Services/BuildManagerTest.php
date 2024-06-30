<?php

namespace Tests\Unit\Services;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\ContentBuilders\Asset\AssetBuilder;
use Terdelyi\Phanstatic\Services\ContentBuilderManager;
use Terdelyi\Phanstatic\Support\Output\OutputInterface;
use Mockery as m;

class BuildManagerTest extends TestCase
{
    private ContentBuilderManager $buildManager;
    private OutputInterface&MockInterface $output;
    private Config&MockInterface $config;

    /**
     */
    protected function setUp(): void
    {
        $this->output = m::mock(OutputInterface::class);
        $this->config = m::mock(Config::class);
        $this->buildManager = new ContentBuilderManager($this->output, $this->config);
    }

    /** @no */
    public function testRun(): void
    {
        $this->expectNotToPerformAssertions();

        $this->config
            ->shouldReceive('getBuilders')
            ->andReturn([AssetBuilder::class]);
        $mockBuilder = m::mock('overload:' . AssetBuilder::class);
        $mockBuilder
            ->shouldReceive('build')->once();

        $this->output
            ->shouldReceive('space')->once();

        $this->buildManager->run();
    }

    public function testGetExecutionTime(): void
    {
        $this->config->expects('getBuilders')->andReturn([]);

        $this->buildManager->run();

        $this->assertIsFloat($this->buildManager->getExecutionTime());
    }
}
