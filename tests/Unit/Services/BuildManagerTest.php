<?php

namespace Tests\Unit\Services;

use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\ContentBuilders\Asset\AssetBuilder;
use Mockery as m;
use Terdelyi\Phanstatic\ContentBuilders\BuilderContextInterface;
use Terdelyi\Phanstatic\ContentBuilders\ContentBuilderManager;
use Terdelyi\Phanstatic\Support\OutputInterface;

class BuildManagerTest extends TestCase
{
    private ContentBuilderManager $buildManager;
    private BuilderContextInterface&MockInterface $context;

    /**
     */
    protected function setUp(): void
    {
        $this->context = m::mock(BuilderContextInterface::class);
        $this->buildManager = new ContentBuilderManager($this->context);
    }

    /** @no */
    public function testRun(): void
    {
        $this->expectNotToPerformAssertions();

        $mockBuilder = m::mock('overload:' . AssetBuilder::class);
        $mockBuilder
            ->shouldReceive('build')->once();

        $this->buildManager->run([]);
    }
}
