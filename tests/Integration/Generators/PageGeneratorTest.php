<?php

declare(strict_types=1);

namespace Integration\Generators;

use Mockery as m;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Generators\PageGenerator;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Phanstatic;
use Terdelyi\Phanstatic\Support\ConfigBuilder;
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

        // TODO: This can only run in isolation because of the Phanstatic::get() calls
        $phanstatic = Phanstatic::init(config: $this->getConfig());

        (new PageGenerator())->run($input, $output);

        $file = $phanstatic->helpers->getBuildDir('index.html');
        static::assertFileExists($file);
        static::assertEquals('This is a test.', file_get_contents($file));
    }

    private function getConfig(): Config
    {
        return ConfigBuilder::make()
            ->setSourceDir('tests/data/content')
            ->setBuildDir('tests/data/dist')
            ->build();
    }
}
