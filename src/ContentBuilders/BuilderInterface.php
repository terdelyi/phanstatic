<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders;

interface BuilderInterface
{
    public function build(): void;
}
