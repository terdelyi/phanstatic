<?php

declare(strict_types=1);

namespace Tests\Unit\New\Commands;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Terdelyi\Phanstatic\New\Commands\PreviewCommand;
use Terdelyi\Phanstatic\New\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\New\Support\Helpers;

/**
 * @internal
 */
class PreviewCommandTest extends TestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $helpers = m::mock(Helpers::class);
        $helpers->shouldReceive('getBuildDir')
            ->andReturn('./tests/data/dist');

        $executor = m::mock(CommandLineExecutor::class);
        $executor->shouldReceive('run')
            ->andReturn(true);

        $command = new PreviewCommand($executor, $helpers);
        $this->commandTester = new CommandTester($command);
    }

    public function testItHasOutput(): void
    {
        $this->commandTester->execute([]);
        $display = trim($this->commandTester->getDisplay());

        $this->assertEquals('', $display);
    }
}
