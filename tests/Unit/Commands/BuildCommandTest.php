<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Terdelyi\Phanstatic\Commands\BuildCommand;
use Terdelyi\Phanstatic\Models\CollectionConfig;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\Time;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class BuildCommandTest extends TestCase
{
    private CommandTester $commandTester;

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

        $helpers = m::mock(Helpers::class);
        $helpers->shouldReceive('getBuildDir')
            ->andReturn('base-url');

        $fileManager = m::mock(Filesystem::class);
        $fileManager->shouldReceive('remove')
            ->andReturn(true);

        $time = m::mock(Time::class);
        $time->shouldReceive('getCurrentTime')
            ->andReturn(0, 0);

        $command = new BuildCommand($config, $helpers, $fileManager, $time);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function itHasOutput(): void
    {
        $this->commandTester->execute([]);

        $display = trim($this->commandTester->getDisplay());
        $expected = <<<'EOT'
            Cleaning out build directory....
            EOT;

        $this->assertStringContainsString($expected, $display);
    }

    #[Test]
    public function itHasOutputWithoutCleanBuildDir(): void
    {
        $this->commandTester->execute(['--no-clean' => true]);

        $display = trim($this->commandTester->getDisplay());
        $expected = <<<'EOT'
            Build completed in 0 seconds
            EOT;

        $this->assertStringContainsString($expected, $display);
    }
}
