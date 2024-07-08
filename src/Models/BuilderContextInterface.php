<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Services\FileManagerInterface;
use Terdelyi\Phanstatic\Support\OutputInterface;

interface BuilderContextInterface
{
    public function getOutput(): OutputInterface;

    public function getConfig(): Config;

    public function getFileManager(): FileManagerInterface;
}
