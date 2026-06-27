<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Test\Fixture\Table;

use TomWilford\DoctrineTestTraits\Fixture\DatabaseTestFixtureInterface;

class EmptyTableFixture implements DatabaseTestFixtureInterface
{
    public function getTableName(): string
    {
        return 'empty_table';
    }

    public function getRecordsToInsert(): array
    {
        return [];
    }
}
