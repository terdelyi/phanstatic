<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

use Terdelyi\Phanstatic\Generators\AssetGenerator;
use Terdelyi\Phanstatic\Generators\Collection\CollectionGenerator;
use Terdelyi\Phanstatic\Generators\Page\PageGenerator;

final class Config
{
    public const DEFAULT_PATH = 'content/config.php';
    public const DEFAULT_SOURCE = 'content';
    public const DEFAULT_BUILD = 'dist';
    public const DEFAULT_URL = 'http://localhost:8080';

    private static ?Config $instance = null;

    public function __construct(
        public ?string $workingDir = null,
        public string $path = self::DEFAULT_PATH,
        public readonly string $sourceDir = self::DEFAULT_SOURCE,
        public readonly string $buildDir = self::DEFAULT_BUILD,
        public readonly string $baseUrl = self::DEFAULT_URL,
        public readonly string $title = '',
        /** @var array<string,mixed> */
        public readonly array $meta = [],
        /** @var array<string,CollectionConfig> */
        public readonly array $collections = [],
        /** @var array<int,string> */
        public readonly array $generators = [
            AssetGenerator::class,
            PageGenerator::class,
            CollectionGenerator::class,
        ],
    ) {}

    public static function init(?Config $config = null): self
    {
        if ($config) {
            return self::$instance = $config;
        }

        return new self();
    }

    public static function get(): Config
    {
        if (self::$instance === null) {
            self::$instance = self::init();
        }

        return self::$instance;
    }
}
