<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419053927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE patient_login_codes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, patient_id INT UNSIGNED DEFAULT NULL, code VARCHAR(6) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, expiration DATETIME NOT NULL, INDEX IDX_D3DE39966B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE patient_login_codes ADD CONSTRAINT FK_D3DE39966B899279 FOREIGN KEY (patient_id) REFERENCES patients (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient_login_codes DROP FOREIGN KEY FK_D3DE39966B899279');
        $this->addSql('DROP TABLE patient_login_codes');
    }
}
