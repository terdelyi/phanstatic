<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

class CommandLineExecutor
{
    public function run(string $command): ?bool
    {
        return passthru($command);
    }
}
