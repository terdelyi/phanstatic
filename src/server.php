<?php

/**
 * @TODO:
 * - Tidy up this file
 * - Move logic to Router
 * - Handle collections
 * - Revisit dd()
 */

declare(strict_types=1);

use Terdelyi\Phanstatic\Support\ConfigLoader;
use Terdelyi\Phanstatic\Support\Router;

include $_composer_autoload_path ?? __DIR__.'/../vendor/autoload.php';

$workingDir = getcwd() ?: dirname(__DIR__);

(new ConfigLoader())->load($workingDir);
(new Router())->handle($_SERVER['REQUEST_URI']);

return false;
