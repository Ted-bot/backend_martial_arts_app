<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240921162527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription ADD IF NOT EXISTS subscription_owned_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT IF EXISTS FK_A3C664D3636ACC40');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3636ACC40 FOREIGN KEY (subscription_owned_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_A3C664D3636ACC40 ON subscription (subscription_owned_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3636ACC40');
        $this->addSql('DROP INDEX IDX_A3C664D3636ACC40');
        $this->addSql('ALTER TABLE subscription DROP subscription_owned_by_id');
    }
}
