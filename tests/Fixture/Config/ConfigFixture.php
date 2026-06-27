<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Test\Fixture\Config;

class ConfigFixture
{
    public function __invoke(): array
    {
        return [
            'db' => [
                'dsn' => 'pdo-sqlite:///:memory:',
            ],
            'doctrine' => [
                'migrations' => [
                    'table_storage' => [
                        'table_name' => 'doctrine_migration_versions',
                        'version_column_name' => 'version',
                        'version_column_length' => 191,
                        'executed_at_column_name' => 'executed_at',
                        'execution_time_column_name' => 'execution_time',
                    ],

                    'migrations_paths' => [
                        'TomWilford\DoctrineTestTraits\Test\Fixture\Migration' => dirname(__DIR__) . '/Migration',
                    ],

                    'all_or_nothing' => true,
                    'transactional' => true,
                    'check_database_platform' => true,
                    'organize_migrations' => 'none',
                    'connection' => null,
                    'em' => null,
                ],
            ],
        ];
    }
}
