<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Fixture;

/**
 * Stores fixtures needed for integration tests in a neat injectable package.
 */
readonly class DatabaseTestFixtureDto
{
    /**
     * @var DatabaseTestFixtureInterface[]
     */
    public array $databaseTestFixtures;

    public function __construct(DatabaseTestFixtureInterface ...$databaseTestFixture)
    {
        $this->databaseTestFixtures = $databaseTestFixture;
    }
}
