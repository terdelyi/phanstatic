<?php

namespace Terdelyi\Phanstatic\Data;

use Terdelyi\Phanstatic\Config\Site;

class RenderData
{
    public function __construct(
        public ?Site       $site = null,
        public ?Page       $page = null,
        public ?Collection $collection = null,
        public ?Pagination $pagination = null,
    )
    {
    }
}