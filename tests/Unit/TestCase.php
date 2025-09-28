<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase as UnitTestCase;

/**
 * @internal
 */
class TestCase extends UnitTestCase
{
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
