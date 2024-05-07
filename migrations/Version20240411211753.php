<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411211753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medical_certificate (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hospital_id INT UNSIGNED DEFAULT NULL, patient_id INT UNSIGNED DEFAULT NULL, doctor_id INT UNSIGNED DEFAULT NULL, cerificate_date DATETIME NOT NULL, impression VARCHAR(255) NOT NULL, purpose VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B36515F863DBB69 (hospital_id), INDEX IDX_B36515F86B899279 (patient_id), INDEX IDX_B36515F887F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medical_certificate ADD CONSTRAINT FK_B36515F863DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE medical_certificate ADD CONSTRAINT FK_B36515F86B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE medical_certificate ADD CONSTRAINT FK_B36515F887F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_certificate DROP FOREIGN KEY FK_B36515F863DBB69');
        $this->addSql('ALTER TABLE medical_certificate DROP FOREIGN KEY FK_B36515F86B899279');
        $this->addSql('ALTER TABLE medical_certificate DROP FOREIGN KEY FK_B36515F887F4FB17');
        $this->addSql('DROP TABLE medical_certificate');
    }
}
