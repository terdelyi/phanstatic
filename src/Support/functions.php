<?php

declare(strict_types=1);

use Terdelyi\Phanstatic\Support\Helpers;

if ( ! function_exists('url')) {
    function url(?string $permalink = null): string
    {
        return (new Helpers())->getBaseUrl($permalink);
    }
}

if ( ! function_exists('asset')) {
    function asset(string $permalink): string
    {
        return (new Helpers())->getAsset($permalink);
    }
}

if ( ! function_exists('source_dir')) {
    function source_dir(?string $path = null, bool $relative = false): string
    {
        return (new Helpers())->getSourceDir($path, $relative);
    }
}

if ( ! function_exists('build_dir')) {
    function build_dir(?string $path = null, bool $relative = false): string
    {
        return (new Helpers())->getBuildDir($path, $relative);
    }
}

if ( ! function_exists('dd')) {
    function dd(mixed ...$args): string
    {
        if (PHP_SAPI !== 'cli') {
            echo '<pre>';
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $file = $trace['file'] ?? 'unknown file';
        $line = $trace['line'] ?? 'unknown line';
        echo "{$file}:{$line}\n";

        foreach ($args as $arg) {
            ob_start();
            var_dump($arg);
            $dump = ob_get_clean() ?: '';
            $dump = preg_replace('/^.*\n/', '', $dump, 1);
            echo $dump."\n";
        }

        if (PHP_SAPI !== 'cli') {
            echo '</pre>';
        }

        exit;
    }
}
