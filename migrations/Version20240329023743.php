<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329023743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctor ADD status VARCHAR(255) DEFAULT \'1\' NOT NULL AFTER address, ADD is_archived TINYINT(1) DEFAULT 1 NOT NULL AFTER address, ADD emp_filename VARCHAR(255) NOT NULL AFTER storage_emp_filename, ADD id_filename VARCHAR(255) NOT NULL AFTER storage_id_filename, DROP filename_employment, DROP filename_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctor ADD filename_employment VARCHAR(255) NOT NULL, ADD filename_id VARCHAR(255) NOT NULL, DROP status, DROP is_archived, DROP emp_filename, DROP id_filename');
    }
}
