<?php

declare(strict_types=1);

namespace TomWilford\DoctrineTestTraits\Test\Fixture\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260627114720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'A table that will remain empty of records';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('empty_table')) {
            $table = $schema->createTable('empty_table');
            $table->addColumn('id', Types::STRING, ['autoincrement' => true]);
            $table->addColumn('value', Types::STRING, ['notnull' => true]);
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('empty_table')) {
            $schema->dropTable('empty_table');
        }
    }
}
