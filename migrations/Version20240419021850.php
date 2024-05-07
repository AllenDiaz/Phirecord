<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419021850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hospital_login_codes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, hospital_id INT UNSIGNED DEFAULT NULL, code VARCHAR(6) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, expiration DATETIME NOT NULL, INDEX IDX_F97775AD63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospital_login_codes ADD CONSTRAINT FK_F97775AD63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospitals (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_login_codes DROP FOREIGN KEY FK_F97775AD63DBB69');
        $this->addSql('DROP TABLE hospital_login_codes');
    }
}
