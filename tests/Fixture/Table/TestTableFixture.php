<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Test\Fixture\Table;

use TomWilford\DbalTestFixtures\Fixture\DatabaseTestFixtureInterface;

class TestTableFixture implements DatabaseTestFixtureInterface
{
    public function getTableName(): string
    {
        return 'test_table';
    }

    public function getRecordsToInsert(): array
    {
        return [
            [
                'id' => 1,
                'value' => 'Record 1',
            ],
            [
                'id' => 99,
                'value' => 'Record 99',
            ],
        ];
    }
}
