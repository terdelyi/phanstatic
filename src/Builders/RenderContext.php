<?php

namespace Terdelyi\Phanstatic\Builders;

use Terdelyi\Phanstatic\Builders\Collection\Collection;
use Terdelyi\Phanstatic\Builders\Collection\Pagination;
use Terdelyi\Phanstatic\Builders\Page\Page;
use Terdelyi\Phanstatic\Config\SiteConfig;

class RenderContext
{
    public function __construct(
        public ?SiteConfig $site = null,
        public ?Page       $page = null,
        public ?Collection $collection = null,
        public ?Pagination $pagination = null,
    ) {}
}
