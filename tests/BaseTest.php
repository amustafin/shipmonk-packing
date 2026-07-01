<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\config\TestBootstrap;

/**
 * This is the base class for all test cases.
 * Put here any common staff, custom assertions, or so
 */
abstract class BaseTest extends TestCase
{
    private Container $diContainer;
    protected function setUp(): void
    {
        $this->diContainer = TestBootstrap::getInstance()->diContainer;
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Resolves service by type.
     * @template T of object
     * @param  class-string<T>  $type
     * @return ?T
     */
    public function getServiceByType(string $type): ?object
    {
        return $this->diContainer->get($type);
    }
}

