<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class CompilerContext
{
    public function __construct(
        public ?Site $site = null,
        public ?Page $page = null,
        public ?Collection $collection = null,
        public ?CollectionPaginator $pagination = null,
    ) {}
}
