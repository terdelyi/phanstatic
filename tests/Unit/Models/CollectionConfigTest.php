<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Terdelyi\Phanstatic\Models\CollectionConfig;
use Tests\Unit\TestCase;

/**
 * @internal
 */
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

        static::assertEquals('Test Title', $collectionConfig->title);
        static::assertEquals('test-title', $collectionConfig->slug);
        static::assertEquals(5, $collectionConfig->pageSize);
    }
}
