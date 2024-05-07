<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420093015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request_medicals (id INT UNSIGNED AUTO_INCREMENT NOT NULL, checkup_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, medicalCertificate_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_65E24CC3263E86E9 (medicalCertificate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE request_medicals ADD CONSTRAINT FK_65E24CC3263E86E9 FOREIGN KEY (medicalCertificate_id) REFERENCES medical_certificates (id)');
        $this->addSql('ALTER TABLE medical_certificates ADD requested TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request_medicals DROP FOREIGN KEY FK_65E24CC3263E86E9');
        $this->addSql('DROP TABLE request_medicals');
        $this->addSql('ALTER TABLE medical_certificates DROP requested');
    }
}
