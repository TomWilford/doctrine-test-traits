<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Test\Fixture\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260627102453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'A simple table that will be populated with records for testing';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('test_table')) {
            $table = $schema->createTable('test_table');
            $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
            $table->addColumn('value', Types::STRING, ['notnull' => true]);
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('test_table')) {
            $schema->dropTable('test_table');
        }
    }
}
