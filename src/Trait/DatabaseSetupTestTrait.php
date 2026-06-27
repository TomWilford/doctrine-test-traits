<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Trait;

use LogicException;
use RuntimeException;

/**
 * Orchestrates the different traits that create and populate the database.
 */
trait DatabaseSetupTestTrait
{
    use DatabaseConnectionTestTrait;
    use DatabaseSchemaTestTrait;
    use DatabaseFixtureTestTrait;

    /**
     * Sets up doctrine/migrations, runs the migrations, and then adds fixtures.
     *
     * @throws RuntimeException|LogicException
     */
    protected function initialiseDatabase(): void
    {
        $this->initialiseDependencyFactory();
        $this->runMigrations();
        $this->insertDefaultFixtureRecords();
    }
}
