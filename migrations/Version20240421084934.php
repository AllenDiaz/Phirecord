<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421084934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctors CHANGE status status VARCHAR(255) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE hospitals CHANGE status status VARCHAR(255) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE patients CHANGE status status VARCHAR(255) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctors CHANGE status status VARCHAR(255) DEFAULT \'2\' NOT NULL');
        $this->addSql('ALTER TABLE hospitals CHANGE status status VARCHAR(255) DEFAULT \'2\' NOT NULL');
        $this->addSql('ALTER TABLE patients CHANGE status status VARCHAR(255) DEFAULT \'1\' NOT NULL');
    }
}
