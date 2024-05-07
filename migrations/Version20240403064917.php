<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403064917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ADD id_filename VARCHAR(255) NOT NULL, ADD id_storage_filename VARCHAR(255) NOT NULL, DROP filename, DROP storage_filename');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ADD filename VARCHAR(255) NOT NULL, ADD storage_filename VARCHAR(255) NOT NULL, DROP id_filename, DROP id_storage_filename');
    }
}
