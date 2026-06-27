<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Trait;

use Doctrine\DBAL\Exception;
use LogicException;
use RuntimeException;
use TomWilford\DbalTestFixtures\Context\DatabaseTestContext;

/**
 * The entrypoint trait for initialising and destroying the database tables.
 */
trait DatabaseTestTrait
{
    use DatabaseSetupTestTrait;
    use DatabaseTearDownTrait;

    protected ?DatabaseTestContext $databaseTestContext = null;

    /**
     * Stores the context locally for the other classes and triggers database initialisation.
     *
     * @param DatabaseTestContext $databaseTestContext
     *
     * @throws LogicException|RuntimeException
     */
    protected function setUpDatabase(DatabaseTestContext $databaseTestContext): void
    {
        if (!$this->databaseTestContext) {
            $this->databaseTestContext = $databaseTestContext;
        }
        $this->initialiseDatabase();
    }

    /**
     * Stores the context locally for the other classes and triggers database destruction.
     *
     * @param DatabaseTestContext $databaseTestContext
     *
     * @throws Exception|LogicException
     */
    protected function tearDownDatabase(DatabaseTestContext $databaseTestContext): void
    {
        if (!$this->databaseTestContext) {
            $this->databaseTestContext = $databaseTestContext;
        }

        $this->dropDatabaseTables();
    }
}
