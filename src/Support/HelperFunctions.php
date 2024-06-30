<?php

use Terdelyi\Phanstatic\Phanstatic;

function url(string $permalink): string
{
    $config = Phanstatic::getConfig();

    if (!str_starts_with($permalink, '/')) {
        $permalink = '/' . $permalink;
    }

    return $config->getBaseUrl($permalink);
}

function asset(string $permalink): string
{
    if (!str_starts_with($permalink, '/')) {
        $permalink = '/' . $permalink;
    }

    $permalink = '/assets' . $permalink;

    return url($permalink);
}
