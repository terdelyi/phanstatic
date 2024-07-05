<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terdelyi\Phanstatic\ContentBuilders\Asset\AssetBuilder;
use Terdelyi\Phanstatic\ContentBuilders\BuilderContextInterface;
use Terdelyi\Phanstatic\ContentBuilders\ContentBuilderManager;

/**
 * @internal
 */
#[CoversClass(ContentBuilderManager::class)]
class BuildManagerTest extends TestCase
{
    private ContentBuilderManager $buildManager;
    private BuilderContextInterface&MockInterface $context;

    protected function setUp(): void
    {
        $this->context = m::mock(BuilderContextInterface::class);
        $this->buildManager = new ContentBuilderManager($this->context);
    }

    /** @no */
    public function testRun(): void
    {
        $this->expectNotToPerformAssertions();

        $mockBuilder = m::mock('overload:'.AssetBuilder::class);
        $mockBuilder
            ->shouldReceive('build')->once();

        $this->buildManager->run([]);
    }
}
