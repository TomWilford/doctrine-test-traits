<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Test\TestCase;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use TomWilford\DbalTestFixtures\Context\DatabaseTestContext;
use TomWilford\DbalTestFixtures\Fixture\DatabaseTestFixtureDto;
use TomWilford\DbalTestFixtures\Test\Trait\FullLibraryAppTestTrait;
use TomWilford\DbalTestFixtures\Trait\DatabaseConnectionTestTrait;
use TomWilford\DbalTestFixtures\Trait\DatabaseFixtureTestTrait;
use TomWilford\DbalTestFixtures\Trait\DatabaseSchemaTestTrait;
use TomWilford\DbalTestFixtures\Trait\DatabaseSetupTestTrait;
use TomWilford\DbalTestFixtures\Trait\DatabaseTearDownTrait;
use TomWilford\DbalTestFixtures\Trait\DatabaseTestTrait;

#[CoversTrait(DatabaseTestTrait::class)]
#[CoversTrait(DatabaseSetupTestTrait::class)]
#[CoversTrait(DatabaseTearDownTrait::class)]
#[CoversTrait(DatabaseConnectionTestTrait::class)]
#[CoversTrait(DatabaseSchemaTestTrait::class)]
#[CoversTrait(DatabaseFixtureTestTrait::class)]
#[CoversClass(DatabaseTestContext::class)]
#[CoversClass(DatabaseTestFixtureDto::class)]
class FullLibraryTest extends TestCase
{
    /**
     * DatabaseTestTrait us used in FullLibraryAppTestTrait.
     *
     * FullLibraryAppTestTrait provides a convenient way to configure DatabaseTestTrait once.
     */
    use FullLibraryAppTestTrait;

    /**
     * public function testFixtureWithoutRecordsRunsSuccessfully(): void
     * {
     *     Although there's not a specific case for it, all of these tests prove a fixture without records does
     *     not break the execution, as FullLibraryAppTestTrait includes EmptyTableFixture in the dto.
     * }
     */
    public function testTableIsCreatedFromMigration(): void
    {
        $connection = self::$context->connection;
        $schemaManager = $connection->createSchemaManager();

        $this->assertTrue(
            $schemaManager->tableExists('test_table')
        );
    }

    public function testTableHasColumnsSpecifiedInMigration(): void
    {
        $connection = self::$context->connection;
        $schemaManager = $connection->createSchemaManager();
        $table = match (true) {
            method_exists($schemaManager, 'introspectTables') => $this->findTableForDoctrine4Plus(
                $schemaManager,
                'test_table'
            ),
            default => $this->findTableForDoctrine3($schemaManager, 'test_table')
        };

        $this->assertTrue($table?->hasColumn('id'));
        $this->assertTrue($table?->hasColumn('value'));
    }

    public function testTableFixturesAreCreated(): void
    {
        $connection = self::$context->connection;
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('test_table');
        $results = $queryBuilder->fetchAllAssociative();

        $this->assertSame(
            [
                [
                    'id' => 1,
                    'value' => 'Record 1',
                ],
                [
                    'id' => 99,
                    'value' => 'Record 99',
                ],
            ],
            $results
        );
    }

    public function testTestMethodEntriesDoNotPersistBetweenTestsPartOne(): void
    {
        $connection = self::$context->connection;
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->insert('test_table')
            ->values([
                'value' => ':value',
            ])
            ->setParameters([
                'value' => 'Record 100',
            ])
        ;

        $queryBuilder->executeQuery();

        $this->assertSame(
            100,
            (int)$connection->lastInsertId()
        );
    }

    public function testTestMethodEntriesDoNotPersistBetweenTestsPartTwo(): void
    {
        $connection = self::$context->connection;
        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('test_table')
            ->where('id = :id')
            ->setParameter('id', 100)
        ;

        $result = $queryBuilder->fetchAllAssociative();

        $this->assertEmpty(
            $result
        );
    }

    public function findTableForDoctrine4Plus(AbstractSchemaManager $schemaManager, string $tableName): ?Table
    {
        $tables = $schemaManager->introspectTables();
        foreach ($tables as $table) {
            if ($table->getObjectName()->getUnqualifiedName()->getValue() === $tableName) {
                return $table;
            }
        }

        return null;
    }

    public function findTableForDoctrine3(AbstractSchemaManager $schemaManager, string $tableName): ?Table
    {
        $tables = $schemaManager->listTables();
        foreach ($tables as $table) {
            if ($table->getName() === $tableName) {
                return $table;
            }
        }

        return null;
    }
}
