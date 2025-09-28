<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Compilers;

use Terdelyi\Phanstatic\Models\CompilerContext;

class PhpCompiler
{
    public function render(string $path, CompilerContext $data): string
    {
        ob_start();

        set_error_handler($this->customErrorHandler());

        try {
            $this->require($path, $data);
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        $output = ob_get_clean();

        restore_error_handler();

        return $output === false ? '' : ltrim($output);
    }

    public function require(string $filePath, CompilerContext $data): int
    {
        return (static function () use ($filePath, $data) {
            $dataVars = get_object_vars($data);
            extract($dataVars, EXTR_SKIP);
            unset($data);

            return require $filePath;
        })();
    }

    public function customErrorHandler(): \Closure
    {
        return function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }
}
