<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Generators;

interface GeneratorInterface
{
    public function run(): void;
}
