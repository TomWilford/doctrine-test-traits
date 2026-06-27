<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Trait;

use LogicException;
use RuntimeException;
use Throwable;
use TomWilford\DbalTestFixtures\Fixture\DatabaseTestFixtureInterface;

/**
 * Persists all the database fixtures to the database.
 */
trait DatabaseFixtureTestTrait
{
    /**
     * Entrypoint to the fixture functionality that loops over the available fixtures and hands off processing.
     *
     * @throws LogicException|RuntimeException
     */
    protected function insertDefaultFixtureRecords(): void
    {
        if ($this->databaseTestContext === null) {
            throw new LogicException(
                'Database test context must be set before processing fixtures.'
            );
        }

        $fixtures = $this->databaseTestContext->databaseTestFixtureDto?->databaseTestFixtures ?? [];
        foreach ($fixtures as $fixture) {
            $this->processFixture($fixture);
        }
    }

    /**
     * Prepares to and loops over each record for a fixture so they can be persisted.
     *
     * @param DatabaseTestFixtureInterface $fixture
     *
     * @throws RuntimeException
     */
    protected function processFixture(DatabaseTestFixtureInterface $fixture): void
    {
        $table = $fixture->getTableName();
        $records = $fixture->getRecordsToInsert();

        foreach ($records as $record) {
            $this->processRecord($record, $table);
        }
    }

    /**
     * Persists the record to the database using doctrine/dbal.
     *
     * Just so I've said it, don't use untrusted data here or link this up to your production database.
     *
     * @param array $record
     * @param string $table
     *
     * @throws RuntimeException
     */
    protected function processRecord(array $record, string $table): void
    {
        try {
            $qb = $this->databaseTestContext->connection->createQueryBuilder();
            $qb->insert($table);

            foreach ($record as $column => $value) {
                $qb->setValue($column, ':' . $column);
                $qb->setParameter($column, $value);
            }

            $qb->executeStatement();
        } catch (Throwable $exception) {
            throw new RuntimeException(
                message: "Failed to insert record into {$table}: " . $exception->getMessage(),
                previous: $exception
            );
        }
    }
}
