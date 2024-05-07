<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329134448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ADD password VARCHAR(255) NOT NULL AFTER birthdate, ADD updated_at DATETIME NOT NULL AFTER hospital_id, ADD approved_at DATETIME DEFAULT NULL AFTER hospital_id, ADD is_archived TINYINT(1) DEFAULT 1 NOT NULL after philhealth_no, ADD status VARCHAR(255) DEFAULT \'1\' NOT NULL AFTER filename' );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient DROP password, DROP updated_at, DROP approved_at, DROP is_archived, DROP status');
    }
}
