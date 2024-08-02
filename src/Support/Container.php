<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Container
{
    private static ?ContainerBuilder $instance = null;

    public static function getInstance(): ContainerBuilder
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        return self::$instance = new ContainerBuilder();
    }
}
