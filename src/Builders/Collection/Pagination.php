<?php

namespace Terdelyi\Phanstatic\Builders\Collection;

class Pagination
{
    public function __construct(
        public ?string $next,
        public ?string $previous,
        public int $current,
        public int $total,
        public bool $isLast,
    ) {}
}
