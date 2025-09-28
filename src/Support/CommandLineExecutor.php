<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Support;

class CommandLineExecutor
{
    public function run(string $command): ?bool
    {
        return passthru($command);
    }
}
