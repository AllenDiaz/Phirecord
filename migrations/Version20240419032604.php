<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419032604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE doctor_login_codes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, doctor_id INT UNSIGNED DEFAULT NULL, code VARCHAR(6) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, expiration DATETIME NOT NULL, INDEX IDX_64E2B22287F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE doctor_login_codes ADD CONSTRAINT FK_64E2B22287F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctor_login_codes DROP FOREIGN KEY FK_64E2B22287F4FB17');
        $this->addSql('DROP TABLE doctor_login_codes');
    }
}
