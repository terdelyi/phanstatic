<?php

declare(strict_types=1);

namespace Tests\Unit\New\Commands;

use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\New\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Terdelyi\Phanstatic\New\Commands\ConfigCommand;
use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\Helpers;

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
        $command = new ConfigCommand($config, $helpers);

        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function itHasOutput(): void
    {
        $this->commandTester->execute([]);
        $display = trim($this->commandTester->getDisplay());
        $expected = <<<'EOT'
            Page title: title
            Base URL: base-url
            Build directory: base-url
            Source directory: base-url
            
            
            Content generators in runtime order:
            - generatorA
            - generatorB
            
            Collections configuration:
            - Test
            
            Site meta data:
            - meta: value
            EOT;

        $this->assertEquals($expected, $display);
    }
}
