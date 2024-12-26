<?php

declare(strict_types=1);

use Terdelyi\Phanstatic\New\Phanstatic;
use Terdelyi\Phanstatic\New\Support\Helpers;

if (!function_exists('url')) {
    function url(?string $permalink = null): string
    {
        /** @var Helpers $helper */
        $helper = Phanstatic::getContainer()->get(Helpers::class);

        return $helper->getBaseUrl($permalink);
    }
}

if (!function_exists('asset')) {
    function asset(string $permalink): string
    {
        /** @var Helpers $helper */
        $helper = Phanstatic::getContainer()->get(Helpers::class);

        $permalink = !\str_starts_with($permalink, '/') ? "/{$permalink}" : $permalink;
        $permalink = '/assets'.$permalink;

        return $helper->getBaseUrl($permalink);
    }
}

if (!function_exists('source_dir')) {
    function source_dir(?string $path = null, bool $relative = false): string
    {
        /** @var Helpers $helper */
        $helper = Phanstatic::getContainer()->get(Helpers::class);

        return $helper->getSourceDir($path, $relative);
    }
}

if (!function_exists('build_dir')) {
    function build_dir(?string $path = null, bool $relative = false): string
    {
        /** @var Helpers $helper */
        $helper = Phanstatic::getContainer()->get(Helpers::class);

        return $helper->getBuildDir($path, $relative);
    }
}
