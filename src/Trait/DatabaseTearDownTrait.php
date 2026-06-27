<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Trait;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use LogicException;

/**
 * Finds and drops all tables in the test database. Inherits the context from DatabaseTestTrait.
 */
trait DatabaseTearDownTrait
{
    /**
     * Creates the schema manager from the context to locate all tables and then triggers the dropping of them.
     *
     * @throws Exception|LogicException
     */
    protected function dropDatabaseTables(): void
    {
        if ($this->databaseTestContext === null) {
            throw new LogicException(
                'Database test context must be set before dropping tables.'
            );
        }
        $schemaManager = $this->databaseTestContext->connection->createSchemaManager();

        $this->disableForeignKeyChecks();

        if ($this->consumerIsUsingDoctrineDbal4Plus($schemaManager)) {
            $this->dropTablesWithIntrospection($schemaManager);
        } else {
            $this->dropTablesWithList($schemaManager);
        }
    }

    /**
     * Disable foreign key checks for drivers that have them so we can safely drop tables in any order.
     *
     * @throws Exception
     */
    private function disableForeignKeyChecks(): void
    {
        $connection = $this->databaseTestContext->connection;
        $platform = $connection->getDatabasePlatform();

        $statement = match (true) {
            $platform instanceof MySQLPlatform => 'SET FOREIGN_KEY_CHECKS=0',
            $platform instanceof SQLitePlatform => 'PRAGMA foreign_keys = OFF',
            $platform instanceof PostgreSQLPlatform => 'SET session_replication_role = replica',
            default => null,
        };

        if ($statement !== null) {
            $connection->executeStatement($statement);
        }
    }

    /**
     * Check if the consuming software is using the preferred introspect method in doctrine/dbal:^4
     */
    private function consumerIsUsingDoctrineDbal4Plus(AbstractSchemaManager $schemaManager): bool
    {
        return method_exists($schemaManager, 'introspectTableNames');
    }

    /**
     * Uses the SchemaManager to find and drop all tables in a doctrine/dbal ~4.4.0 context
     *
     * @param AbstractSchemaManager $schemaManager
     *
     * @throws Exception
     */
    private function dropTablesWithIntrospection(AbstractSchemaManager $schemaManager): void
    {
        $schema = $schemaManager->introspectTableNames();
        foreach ($schema as $item) {
            $schemaManager->dropTable($item->toString());
        }
    }

    /**
     * Uses the SchemaManager to find and drop all tables in a doctrine/dbal ~3.10.0 context
     *
     * @param AbstractSchemaManager $schemaManager
     *
     * @throws Exception
     */
    private function dropTablesWithList(AbstractSchemaManager $schemaManager): void
    {
        $schema = $schemaManager->listTableNames();
        foreach ($schema as $item) {
            $schemaManager->dropTable($item);
        }
    }
}
