<?php

declare(strict_types=1);

namespace Tests\Unit\New\Compilers;

use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\New\TestCase;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\RenderContext;
use Terdelyi\Phanstatic\New\Compilers\PhpCompiler;

/**
 * @internal
 */
class PhpCompilerTest extends TestCase
{
    private PhpCompiler $phpCompiler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phpCompiler = new PhpCompiler();
    }

    #[Test]
    public function canRenderATemplate(): void
    {
        $path = './tests/data/content/pages/template.php';
        $data = new RenderContext(
            null,
            new Page(
                'path-to-page',
                'relative-path',
                'link',
                'url',
            )
        );

        $output = $this->phpCompiler->render($path, $data);

        $expected = <<<'EOT'
            Page path: path-to-page
            Page relative path: relative-path
            Page permalink: link
            Page url: url
            EOT;

        $this->assertEquals($expected, $output);
    }

    #[Test]
    public function renderRunsIntoError(): void
    {
        $this->expectException(\ParseError::class);

        $path = './tests/data/content/pages/template-with-error.php';
        $data = new RenderContext(
            null,
            new Page(
                'path-to-page',
                'relative-path',
                'link',
                'url',
            )
        );

        try {
            $this->phpCompiler->render($path, $data);
        } finally {
            restore_error_handler();
        }
    }

    #[Test]
    public function canRequireATemplate(): void
    {
        $this->expectsOutput();

        $path = './tests/data/content/pages/template.php';

        $data = new RenderContext(
            null,
            new Page(
                'path-to-page',
                'relative-path',
                'link',
                'url',
            )
        );

        $expected = <<<'EOT'
            Page path: path-to-page
            Page relative path: relative-path
            Page permalink: link
            Page url: url
            EOT;

        ob_start();

        try {
            $this->phpCompiler->require($path, $data);
        } finally {
            $result = ob_get_clean();
        }

        $this->assertEquals($expected, $result);
    }
}
