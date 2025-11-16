<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

class CompilerContext
{
    public function __construct(
        public Site $site,
        public Page $page,
        public ?Collection $collection = null,
        public ?CollectionPaginator $pagination = null,
    ) {}
}
