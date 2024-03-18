<?php

namespace Terdelyi\Phanstatic\Data;
class Pagination
{
    public function __construct(
        public ?string $next,
        public ?string $previous,
        public int $current,
        public int $total,
        public bool $isLast,
    ){}
}