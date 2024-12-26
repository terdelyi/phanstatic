<?php

namespace Tests\Unit\New\Models;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\New\Models\CollectionConfig;
use Tests\Unit\New\TestCase;

class CollectionConfigTest extends TestCase
{
    #[Test]
    public function itCanCreateInstance(): void
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
