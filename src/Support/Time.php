<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Support;

class Time
{
    public function getCurrentTime(): float
    {
        return microtime(true);
    }
}
