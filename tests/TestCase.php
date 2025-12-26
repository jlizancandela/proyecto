<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery;

/**
 * Base TestCase class.
 * @property \PDO|\Mockery\MockInterface $pdo
 * @property \PDOStatement|\Mockery\MockInterface $stmt
 * @property \Reservas\Infrastructure\ReservaRepository $repository
 */
abstract class TestCase extends BaseTestCase
{
    protected $pdo;
    protected $stmt;
    protected $repository;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
