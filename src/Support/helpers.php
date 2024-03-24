<?php

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\Container;

function url(string $permalink): string
{
    /** @var Config $config */
    $config = Container::get('config');

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
