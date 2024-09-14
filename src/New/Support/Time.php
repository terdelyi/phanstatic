<?php

namespace Terdelyi\Phanstatic\New\Support;

class Time
{
    public function getCurrentTime(): float
    {
        return microtime(true);
    }
}