<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Trait;

use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\DependencyFactory;
use LogicException;

/**
 * Prepare the connection for doctrine/migrations to work.
 */
trait DatabaseConnectionTestTrait
{
    protected DependencyFactory $dependencyFactory;

    /**
     * Sets up the doctrine/migrations DependencyFactory with the database connection and migration
     * configuration details from the context class.
     */
    protected function initialiseDependencyFactory(): void
    {
        if ($this->databaseTestContext === null) {
            throw new LogicException(
                'Database test context must be set before initialising the dependency factory.'
            );
        }

        $configurationLoader = $this->databaseTestContext->configurationLoader;
        $connection = $this->databaseTestContext->connection;
        $existingConnection = new ExistingConnection($connection);

        $this->dependencyFactory = DependencyFactory::fromConnection(
            $configurationLoader,
            $existingConnection
        );
    }
}
