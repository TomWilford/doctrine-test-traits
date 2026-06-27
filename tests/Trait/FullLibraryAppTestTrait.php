<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Test\Trait;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use TomWilford\DoctrineTestTraits\Context\DatabaseTestContext;
use TomWilford\DoctrineTestTraits\Fixture\DatabaseTestFixtureDto;
use TomWilford\DoctrineTestTraits\Test\Fixture\Config\ConfigFixture;
use TomWilford\DoctrineTestTraits\Test\Fixture\Table\TestTableFixture;
use TomWilford\DoctrineTestTraits\Trait\DatabaseTestTrait;

/**
 * Helper class that configures the DatabaseTestTrait and implements it in phpunit's setUp and tearDown methods
 */
trait FullLibraryAppTestTrait
{
    use DatabaseTestTrait;

    protected static ?DatabaseTestContext $context = null;

    /**
     * Configures phpunit's setUp method
     */
    protected function setUp(): void
    {
        $this->setUpDatabase($this->createContext());
    }

    /**
     * Configures phpunit's tearDown method
     */
    protected function tearDown(): void
    {
        $this->tearDownDatabase($this->createContext());

        parent::tearDown();
    }

    /**
     * Builds the context class directly in FullLibraryAppTestTrait
     */
    protected function createContext(): DatabaseTestContext
    {
        if (!self::$context) {
            $settings = (new ConfigFixture())();

            $connectionParams = (new DsnParser())->parse($settings['db']['dsn']);
            $connection = DriverManager::getConnection($connectionParams);

            $config = new ConfigurationArray($settings['doctrine']['migrations']);

            self::$context = new DatabaseTestContext(
                $connection,
                $config,
                new DatabaseTestFixtureDto(
                    new TestTableFixture()
                )
            );
        }

        return self::$context;
    }
}
