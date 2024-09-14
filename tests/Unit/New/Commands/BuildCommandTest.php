<?php

declare(strict_types=1);

namespace Tests\Unit\New\Commands;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Terdelyi\Phanstatic\New\Commands\BuildCommand;
use Terdelyi\Phanstatic\New\Commands\ConfigCommand;
use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\FileManager;
use Terdelyi\Phanstatic\New\Support\Helpers;
use Terdelyi\Phanstatic\New\Support\Time;

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

        $fileManager = m::mock(FileManager::class);
        $fileManager->shouldReceive('cleanDirectory')
            ->andReturn(true);

        $time = m::mock(Time::class);
        $time->shouldReceive('getCurrentTime')
            ->andReturn(0, 0);

        $command = new BuildCommand($config, $helpers, $fileManager, $time);

        $this->commandTester = new CommandTester($command);
    }

    public function testItHasOutput(): void
    {
        $this->commandTester->execute([]);
        $display = trim($this->commandTester->getDisplay());
        $expected = <<<'EOT'
            Cleaning out build directory....

            generatorA
            generatorB
            <green>Build completed in 0 seconds</green>
            EOT;

        $this->assertEquals($expected, $display);
    }

    public function testItHasOutputWithoutCleanBuildDir(): void
    {
        $this->commandTester->execute(['--no-clean' => true]);
        $display = trim($this->commandTester->getDisplay());
        $expected = <<<'EOT'
            generatorA
            generatorB
            <green>Build completed in 0 seconds</green>
            EOT;

        $this->assertEquals($expected, $display);
    }
}
