<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301011327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, birthdate DATETIME NOT NULL, gender VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact_number INT NOT NULL, filename VARCHAR(255) NOT NULL, storage_filename VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doctor (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, birthdate DATETIME NOT NULL, gender VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact INT NOT NULL, address VARCHAR(255) NOT NULL, filename_employment VARCHAR(255) NOT NULL, filename_id VARCHAR(255) NOT NULL, storage_emp_filename VARCHAR(255) NOT NULL, storage_id_filename VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hospital (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact_number INT NOT NULL, filename VARCHAR(255) NOT NULL, storage_filename VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, birthdate DATETIME NOT NULL, gender VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact INT NOT NULL, address VARCHAR(255) NOT NULL, guardian_name VARCHAR(255) NOT NULL, philhealth_no VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, storage_filename VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE doctor');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP TABLE patient');
    }
}
