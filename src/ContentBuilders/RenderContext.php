<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\ContentBuilders;

use Terdelyi\Phanstatic\Config\SiteConfig;
use Terdelyi\Phanstatic\ContentBuilders\Collection\Collection;
use Terdelyi\Phanstatic\ContentBuilders\Collection\CollectionPaginator;
use Terdelyi\Phanstatic\ContentBuilders\Page\Page;

class RenderContext
{
    public function __construct(
        public ?SiteConfig $site = null,
        public ?Page $page = null,
        public ?Collection $collection = null,
        public ?CollectionPaginator $pagination = null,
    ) {}
}
