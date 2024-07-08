<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

use Terdelyi\Phanstatic\Config\SiteConfig;

class RenderContext
{
    public function __construct(
        public ?SiteConfig $site = null,
        public ?Page $page = null,
        public ?Collection $collection = null,
        public ?CollectionPaginator $pagination = null,
    ) {}
}
