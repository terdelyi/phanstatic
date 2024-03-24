<?php

namespace Terdelyi\Phanstatic\Services;

class Container
{
    /**
     * @var array<string,object>
     */
    private static array $instances = [];

    public static function set(string $name, object $instance): void
    {
        self::$instances[$name] = $instance;
    }

    public static function get(string $name): ?object
    {
        return self::$instances[$name] ?? null;
    }
}
