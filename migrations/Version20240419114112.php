<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419114112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE request_checkups (id INT UNSIGNED AUTO_INCREMENT NOT NULL, checkup_code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, prenatalCheckup_id INT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_A8BF8A2B61551F43 (prenatalCheckup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE request_checkups ADD CONSTRAINT FK_A8BF8A2B61551F43 FOREIGN KEY (prenatalCheckup_id) REFERENCES prenatal_checkups (id)');
        $this->addSql('ALTER TABLE prenatal_checkups ADD requested TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request_checkups DROP FOREIGN KEY FK_A8BF8A2B61551F43');
        $this->addSql('DROP TABLE request_checkups');
        $this->addSql('ALTER TABLE prenatal_checkups DROP requested');
    }
}
