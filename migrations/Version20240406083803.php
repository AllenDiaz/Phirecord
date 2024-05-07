<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240406083803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admission_form (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hospital_id INT UNSIGNED DEFAULT NULL, patient_id INT UNSIGNED DEFAULT NULL, doctor_id INT UNSIGNED DEFAULT NULL, family_member VARCHAR(255) NOT NULL, symptoms VARCHAR(255) NOT NULL, blood_pressure VARCHAR(255) NOT NULL, temperature VARCHAR(255) NOT NULL, weight VARCHAR(255) NOT NULL, respiratory_rate VARCHAR(255) NOT NULL, pulse_rate VARCHAR(255) NOT NULL, oxygen_saturation VARCHAR(255) NOT NULL, diagnosis VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E3C307F63DBB69 (hospital_id), INDEX IDX_E3C307F6B899279 (patient_id), INDEX IDX_E3C307F87F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admission_form_old (id INT UNSIGNED NOT NULL, hospital_id INT UNSIGNED DEFAULT NULL, family_member VARCHAR(255) NOT NULL, patientName VARCHAR(255) NOT NULL, doctorName VARCHAR(255) NOT NULL, symptoms VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, blood_pressure VARCHAR(255) NOT NULL, temperature VARCHAR(255) NOT NULL, weight VARCHAR(255) NOT NULL, respiratory_rate VARCHAR(255) NOT NULL, pulse_rate VARCHAR(255) NOT NULL, oxygen_saturation VARCHAR(255) NOT NULL, diagnosis VARCHAR(255) NOT NULL, INDEX IDX_F14ACCBD63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admission_form ADD CONSTRAINT FK_E3C307F63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE admission_form ADD CONSTRAINT FK_E3C307F6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE admission_form ADD CONSTRAINT FK_E3C307F87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
        $this->addSql('ALTER TABLE admission_form_old ADD CONSTRAINT FK_F14ACCBD63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE doctor CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'default-picture.jpg\' NOT NULL');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB87F4FB17');
        $this->addSql('DROP INDEX IDX_1ADAD7EB87F4FB17 ON patient');
        $this->addSql('ALTER TABLE patient DROP doctor_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admission_form DROP FOREIGN KEY FK_E3C307F63DBB69');
        $this->addSql('ALTER TABLE admission_form DROP FOREIGN KEY FK_E3C307F6B899279');
        $this->addSql('ALTER TABLE admission_form DROP FOREIGN KEY FK_E3C307F87F4FB17');
        $this->addSql('ALTER TABLE admission_form_old DROP FOREIGN KEY FK_F14ACCBD63DBB69');
        $this->addSql('DROP TABLE admission_form');
        $this->addSql('DROP TABLE admission_form_old');
        $this->addSql('ALTER TABLE doctor CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'default-picture.jpeg\' NOT NULL');
        $this->addSql('ALTER TABLE patient ADD doctor_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1ADAD7EB87F4FB17 ON patient (doctor_id)');
    }
}
