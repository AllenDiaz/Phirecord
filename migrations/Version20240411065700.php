<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411065700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prenatal_checkup (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hospital_id INT UNSIGNED DEFAULT NULL, patient_id INT UNSIGNED DEFAULT NULL, doctor_id INT UNSIGNED DEFAULT NULL, family_member VARCHAR(255) NOT NULL, last_menstrual_date DATETIME DEFAULT NULL, confine_date_estimated DATETIME NOT NULL, fetal_heart_tones VARCHAR(255) NOT NULL, gravida VARCHAR(255) NOT NULL, para VARCHAR(255) NOT NULL, labaratory VARCHAR(255) DEFAULT \'N/A\' NOT NULL, urinalysis VARCHAR(255) DEFAULT \'N/A\' NOT NULL, blood_count VARCHAR(255) DEFAULT \'N/A\' NOT NULL, fecalysis VARCHAR(255) DEFAULT \'N/A\' NOT NULL, isPrescribed VARCHAR(255) DEFAULT \'\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, checkup_date DATETIME NOT NULL, INDEX IDX_F463FA8863DBB69 (hospital_id), INDEX IDX_F463FA886B899279 (patient_id), INDEX IDX_F463FA8887F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prenatal_checkup ADD CONSTRAINT FK_F463FA8863DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE prenatal_checkup ADD CONSTRAINT FK_F463FA886B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE prenatal_checkup ADD CONSTRAINT FK_F463FA8887F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prenatal_checkup DROP FOREIGN KEY FK_F463FA8863DBB69');
        $this->addSql('ALTER TABLE prenatal_checkup DROP FOREIGN KEY FK_F463FA886B899279');
        $this->addSql('ALTER TABLE prenatal_checkup DROP FOREIGN KEY FK_F463FA8887F4FB17');
        $this->addSql('DROP TABLE prenatal_checkup');
    }
}
