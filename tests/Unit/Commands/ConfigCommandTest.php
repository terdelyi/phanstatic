<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use Terdelyi\Phanstatic\Commands\ConfigCommand;
use Terdelyi\Phanstatic\Models\CollectionConfig;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class ConfigCommandTest extends TestCase
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
        $helpers->shouldReceive('getBaseUrl')
            ->andReturn('base-url');
        $helpers->shouldReceive('getBuildDir')
            ->andReturn('build-dir');
        $helpers->shouldReceive('getSourceDir')
            ->andReturn('source-dir');
        $command = new ConfigCommand($config, $helpers);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function itHasOutput(): void
    {
        $this->commandTester->execute([]);
        $display = trim($this->commandTester->getDisplay());
        $expected = <<<'EOT'
            Loaded content generators
            EOT;

        $this->assertStringContainsString($expected, $display);
    }
}
