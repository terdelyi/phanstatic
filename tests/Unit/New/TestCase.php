<?php

namespace Tests\Unit\New;

use Mockery as m;
use PHPUnit\Framework\TestCase as UnitTestCase;

class TestCase extends UnitTestCase
{
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}