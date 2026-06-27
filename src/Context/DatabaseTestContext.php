<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Context;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use TomWilford\DoctrineTestTraits\Fixture\DatabaseTestFixtureDto;

/**
 * A simple value object with all the details required to use DatabaseTestTrait.
 *
 * A DatabaseTestFixtureDto is optional in case tables do not need to be populated with fixture data.
 */
readonly class DatabaseTestContext
{
    public function __construct(
        public Connection $connection,
        public ConfigurationLoader $configurationLoader,
        public ?DatabaseTestFixtureDto $databaseTestFixtureDto = null,
    ) {
    }
}
