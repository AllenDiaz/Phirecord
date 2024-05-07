<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401015723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` ADD is_head_admin TINYINT(1) DEFAULT 1 NOT NULL, ADD profile_picture VARCHAR(255) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE doctor ADD profile_picture VARCHAR(255) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE patient CHANGE philhealth_no philhealth_no VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctor DROP profile_picture');
        $this->addSql('ALTER TABLE patient CHANGE philhealth_no philhealth_no VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `admin` DROP is_head_admin, DROP profile_picture');
    }
}
