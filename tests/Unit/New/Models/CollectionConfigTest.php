<?php

namespace Tests\Unit\New\Models;

use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use PHPUnit\Framework\TestCase;

class CollectionConfigTest extends TestCase
{
    public function testItCanCreateInstance(): void
    {
        $collectionConfig = new CollectionConfig(
            'Test Title',
            'test-title',
            5
        );

        $this->assertEquals('Test Title', $collectionConfig->title);
        $this->assertEquals('test-title', $collectionConfig->slug);
        $this->assertEquals(5, $collectionConfig->pageSize);
    }
}
