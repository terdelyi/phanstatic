<?php

use Terdelyi\Phanstatic\Config\Config;

function url(string $permalink): string
{
    $config = Config::getInstance();

    if (!str_starts_with($permalink, '/')) {
        $permalink = '/' . $permalink;
    }

    return $config->getSite()->baseUrl . $permalink;
}

function asset(string $permalink): string
{
    if (!str_starts_with($permalink, '/')) {
        $permalink = '/' . $permalink;
    }

    $permalink = '/assets' . $permalink;

    return url($permalink);
}
