<?php

declare(strict_types=1);

namespace TomWilford\DbalTestFixtures\Trait;

use Doctrine\Migrations\Metadata\MigrationPlanList;
use Doctrine\Migrations\MigratorConfiguration;
use LogicException;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Executes the doctrine migrations.
 */
trait DatabaseSchemaTestTrait
{
    private const MIGRATION_TARGET = 'latest';

    /**
     * Run all the migrations up to the most recent one using the 'latest' version alias.
     *
     * @throws LogicException
     */
    protected function runMigrations(): void
    {
        if ($this->databaseTestContext === null) {
            throw new LogicException(
                'Database test context must be set before running migrations.'
            );
        }

        $plan = $this->buildMigrationPlan();
        $migrator = $this->dependencyFactory->getMigrator();
        $migratorConfiguration = $this->buildMigratorConfiguration();
        $this->dependencyFactory->getMetadataStorage()->ensureInitialized();

        $migrator->migrate($plan, $migratorConfiguration);
    }

    /**
     * Plans out the migration to the latest version.
     */
    private function buildMigrationPlan(): MigrationPlanList
    {
        $version = $this->dependencyFactory->getVersionAliasResolver()->resolveVersionAlias(self::MIGRATION_TARGET);
        $planCalculator = $this->dependencyFactory->getMigrationPlanCalculator();

        return $planCalculator->getPlanUntilVersion($version);
    }

    /**
     * Creates the MigratorConfiguration using a dummy ArrayInput as we are not in a cli context.
     *
     * Note: MigratorConfiguration is marked as internal so this may break in the future
     */
    public function buildMigratorConfiguration(): MigratorConfiguration
    {
        $migratorConfigurationFactory = $this->dependencyFactory->getConsoleInputMigratorConfigurationFactory();

        return $migratorConfigurationFactory->getMigratorConfiguration(new ArrayInput([]));
    }
}
