<?php

namespace Terdelyi\Phanstatic\ContentBuilders;

use Terdelyi\Phanstatic\Config\Config;
use Terdelyi\Phanstatic\Support\Output\OutputInterface;

interface BuilderContextInterface
{
    public function getOutput(): OutputInterface;
    public function getConfig(): Config;
}
