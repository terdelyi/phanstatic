<?php

declare(strict_types=1);

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Support\Container;

/**
 * @throws Exception
 */
function url(string $permalink): string
{
    $container = Container::getInstance();

    /** @var Config $config */
    $config = $container->get('config');

    if (!str_starts_with($permalink, '/')) {
        $permalink = '/'.$permalink;
    }

    return $config->getBaseUrl($permalink);
}

/**
 * @throws Exception
 */
function asset(string $permalink): string
{
    if (!str_starts_with($permalink, '/')) {
        $permalink = '/'.$permalink;
    }

    $permalink = '/assets'.$permalink;

    return url($permalink);
}
