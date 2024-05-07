<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413043222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prenatal_checkups CHANGE gravida gravida VARCHAR(255) DEFAULT NULL, CHANGE para para VARCHAR(255) DEFAULT NULL, CHANGE labaratory labaratory VARCHAR(255) DEFAULT \'N/A\' NOT NULL, CHANGE urinalysis urinalysis VARCHAR(255) DEFAULT \'N/A\' NOT NULL, CHANGE blood_count blood_count VARCHAR(255) DEFAULT \'N/A\' NOT NULL, CHANGE fecalysis fecalysis VARCHAR(255) DEFAULT \'N/A\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prenatal_checkups CHANGE gravida gravida VARCHAR(255) NOT NULL, CHANGE para para VARCHAR(255) NOT NULL, CHANGE labaratory labaratory VARCHAR(255) DEFAULT \'NA\' NOT NULL, CHANGE urinalysis urinalysis VARCHAR(255) DEFAULT \'NA\' NOT NULL, CHANGE blood_count blood_count VARCHAR(255) DEFAULT \'NA\' NOT NULL, CHANGE fecalysis fecalysis VARCHAR(255) DEFAULT \'NA\' NOT NULL');
    }
}
