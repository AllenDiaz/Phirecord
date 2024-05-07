<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240414082042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE referral DROP FOREIGN KEY FK_73079D0087F4FB17');
        $this->addSql('DROP INDEX IDX_73079D0087F4FB17 ON referral');
        $this->addSql('ALTER TABLE referral DROP doctor_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE referral ADD doctor_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE referral ADD CONSTRAINT FK_73079D0087F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_73079D0087F4FB17 ON referral (doctor_id)');
    }
}
