<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417085513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request_admissions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, admission_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, admissionForm_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_C50A1B3D53EB9128 (admissionForm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE request_admissions ADD CONSTRAINT FK_C50A1B3D53EB9128 FOREIGN KEY (admissionForm_id) REFERENCES admission_forms (id)');
        $this->addSql('ALTER TABLE admission_forms ADD requested TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request_admissions DROP FOREIGN KEY FK_C50A1B3D53EB9128');
        $this->addSql('DROP TABLE request_admissions');
        $this->addSql('ALTER TABLE admission_forms DROP requested');
    }
}
