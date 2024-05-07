<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240414054338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE referral (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hospital_id INT UNSIGNED DEFAULT NULL, patient_id INT UNSIGNED DEFAULT NULL, doctor_id INT UNSIGNED DEFAULT NULL, refferal_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, isAccepted TINYINT(1) DEFAULT 0 NOT NULL, to_hospital INT NOT NULL, INDEX IDX_73079D0063DBB69 (hospital_id), INDEX IDX_73079D006B899279 (patient_id), INDEX IDX_73079D0087F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referral ADD CONSTRAINT FK_73079D0063DBB69 FOREIGN KEY (hospital_id) REFERENCES hospitals (id)');
        $this->addSql('ALTER TABLE referral ADD CONSTRAINT FK_73079D006B899279 FOREIGN KEY (patient_id) REFERENCES patients (id)');
        $this->addSql('ALTER TABLE referral ADD CONSTRAINT FK_73079D0087F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE referral DROP FOREIGN KEY FK_73079D0063DBB69');
        $this->addSql('ALTER TABLE referral DROP FOREIGN KEY FK_73079D006B899279');
        $this->addSql('ALTER TABLE referral DROP FOREIGN KEY FK_73079D0087F4FB17');
        $this->addSql('DROP TABLE referral');
    }
}
