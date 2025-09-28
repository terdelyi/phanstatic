<?php

declare(strict_types=1);

namespace Tests\Unit\New\Commands;

use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use Terdelyi\Phanstatic\New\Commands\PreviewCommand;
use Terdelyi\Phanstatic\New\Support\CommandLineExecutor;
use Terdelyi\Phanstatic\New\Support\Helpers;
use Tests\Unit\New\TestCase;

/**
 * @internal
 */
class PreviewCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private Helpers|MockInterface $helpers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helpers = m::mock(Helpers::class);

        $executor = m::mock(CommandLineExecutor::class);
        $executor->shouldReceive('run')
            ->andReturn(true);

        $command = new PreviewCommand($executor, $this->helpers);
        $this->commandTester = new CommandTester($command);
    }

    #[Test]
    public function itHasOutput(): void
    {
        $this->helpers->shouldReceive('getBuildDir')
            ->andReturn('./tests/data/dist');

        $this->commandTester->execute([]);
        $this->commandTester->assertCommandIsSuccessful();
    }

    #[Test]
    public function itFailsWhenPublicPathNotFound(): void
    {
        $this->helpers->shouldReceive('getBuildDir')
            ->andReturn('invalid-path');

        $this->commandTester->execute([]);

        $display = trim($this->commandTester->getDisplay());

        $this->assertEquals('Directory invalid-path does not exist. Have you run build before?', $display);
    }
}
