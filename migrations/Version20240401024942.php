<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401024942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` CHANGE is_head_admin is_head_admin TINYINT(1) DEFAULT 0 NOT NULL, CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'default-picture.jpeg\' NOT NULL');
        $this->addSql('ALTER TABLE doctor CHANGE is_archived is_archived TINYINT(1) DEFAULT 0 NOT NULL, CHANGE status status VARCHAR(255) DEFAULT \'2\' NOT NULL, CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'default-picture.jpeg\' NOT NULL');
        $this->addSql('ALTER TABLE hospital ADD profile_picture VARCHAR(255) DEFAULT \'default-picture.jpeg\' NOT NULL, CHANGE status status VARCHAR(255) DEFAULT \'2\' NOT NULL, CHANGE is_archived is_archived TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital DROP profile_picture, CHANGE status status VARCHAR(255) DEFAULT \'1\' NOT NULL, CHANGE is_archived is_archived TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE doctor CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'1\' NOT NULL, CHANGE status status VARCHAR(255) DEFAULT \'1\' NOT NULL, CHANGE is_archived is_archived TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE `admin` CHANGE is_head_admin is_head_admin TINYINT(1) DEFAULT 1 NOT NULL, CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'1\' NOT NULL');
    }
}
