<?php

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Concerns\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshDatabaseSetUp();
    }

    protected function tearDown(): void
    {
        $this->refreshDatabaseTearDown();
        parent::tearDown();
    }
}
