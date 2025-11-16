<?php

declare(strict_types=1);

namespace Tests\Unit\Compilers;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Compilers\PhpCompiler;
use Terdelyi\Phanstatic\Models\CompilerContext;
use Terdelyi\Phanstatic\Models\Page;
use Terdelyi\Phanstatic\Models\Site;
use Tests\Unit\TestCase;

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
        $path = self::$dataPath.'/pages/template.php';
        $data = new CompilerContext(
            new Site(
                'Test Site',
                'http://example.com',
            ),
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

        static::assertEquals($expected, $output);
    }

    #[Test]
    public function renderRunsIntoError(): void
    {
        $this->expectException(\ParseError::class);

        $path = self::$dataPath.'/pages/template-with-error.php';
        $data = new CompilerContext(
            new Site(
                'Test Site',
                'http://example.com',
            ),
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

        $path = self::$dataPath.'/pages/template.php';

        $data = new CompilerContext(
            new Site(
                'Test Site',
                'http://example.com',
            ),
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

        static::assertEquals($expected, $result);
    }
}
