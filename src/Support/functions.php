<?php

declare(strict_types=1);

use Terdelyi\Phanstatic\Phanstatic;

if ( ! function_exists('url')) {
    function url(?string $permalink = null): string
    {
        return Phanstatic::get()->helpers->getBaseUrl($permalink);
    }
}

if ( ! function_exists('asset')) {
    function asset(string $permalink): string
    {
        return Phanstatic::get()->helpers->getAsset($permalink);
    }
}

if ( ! function_exists('source_dir')) {
    function source_dir(?string $path = null, bool $relative = false): string
    {
        return Phanstatic::get()->helpers->getSourceDir($path, $relative);
    }
}

if ( ! function_exists('build_dir')) {
    function build_dir(?string $path = null, bool $relative = false): string
    {
        return Phanstatic::get()->helpers->getBuildDir($path, $relative);
    }
}

if ( ! function_exists('dd')) {
    function dd(...$arg): string
    {
        var_dump($arg);

        exit;
    }
}
