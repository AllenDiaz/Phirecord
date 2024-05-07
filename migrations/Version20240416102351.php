<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240416102351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prescriptions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, storage_filename VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, prenatalCheckup_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_E41E1AC361551F43 (prenatalCheckup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC361551F43 FOREIGN KEY (prenatalCheckup_id) REFERENCES prenatal_checkups (id)');
        $this->addSql('ALTER TABLE prenatal_checkups CHANGE fetal_heart_tones fetal_heart_tones VARCHAR(255) DEFAULT \'N/A\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC361551F43');
        $this->addSql('DROP TABLE prescriptions');
        $this->addSql('ALTER TABLE prenatal_checkups CHANGE fetal_heart_tones fetal_heart_tones VARCHAR(255) NOT NULL');
    }
}
