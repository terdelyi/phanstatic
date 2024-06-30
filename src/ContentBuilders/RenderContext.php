<?php

namespace Terdelyi\Phanstatic\ContentBuilders;

use Terdelyi\Phanstatic\ContentBuilders\Collection\Collection;
use Terdelyi\Phanstatic\ContentBuilders\Collection\CollectionPaginator;
use Terdelyi\Phanstatic\ContentBuilders\Page\Page;
use Terdelyi\Phanstatic\Config\SiteConfig;

class RenderContext
{
    public function __construct(
        public ?SiteConfig          $site = null,
        public ?Page                $page = null,
        public ?Collection          $collection = null,
        public ?CollectionPaginator $pagination = null,
    ) {}
}
