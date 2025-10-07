<?php

declare(strict_types=1);

namespace Integration\Generators;

use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Generators\PageGenerator;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Tests\Unit\TestCase;

/**
 * @internal
 */
class PageGeneratorTest extends TestCase
{
    protected bool $cleanUp = true;

    #[Test]
    public function canRun(): void
    {
        $input = m::mock(InputInterface::class);

        $output = m::mock(OutputInterface::class);
        $output->shouldReceive('writeln');

        Config::init($this->getConfig());
        (new PageGenerator())->run($input, $output);

        $file = (new Helpers())->getBuildDir('index.html');

        static::assertFileExists($file);
        static::assertEquals('This is a test.', file_get_contents($file));
    }

    private function getConfig(): Config
    {
        return new Config(
            sourceDir: 'tests/data/content',
            buildDir: 'tests/data/dist',
        );
    }
}
