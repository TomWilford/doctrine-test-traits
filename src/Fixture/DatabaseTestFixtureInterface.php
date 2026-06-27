<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Fixture;

/**
 * The shape a database fixture entry requires to add records when setting up.
 */
interface DatabaseTestFixtureInterface
{
    /**
     * The name of the table to add the records to.
     */
    public function getTableName(): string;

    /**
     * The records to be added to the table.
     *
     * Must be an array of arrays, with key/value pairs for the column name and the value to be inserted
     *
     * @return array<array<string, mixed>>
     */
    public function getRecordsToInsert(): array;
}
