<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410083810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admission_form ADD admission_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE patient CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'default-picture.jpg\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admission_form DROP admission_date');
        $this->addSql('ALTER TABLE patient CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'1\' NOT NULL');
    }
}
