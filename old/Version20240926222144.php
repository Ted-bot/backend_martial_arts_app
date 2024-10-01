<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926222144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new UUID field to token_manager table with constraints';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token_manager ADD IF NOT EXISTS uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE token_manager ADD IF NOT EXISTS user_profile_id INT NOT NULL');
        $this->addSql('ALTER TABLE token_manager DROP CONSTRAINT FK_142B3C3A6B9DD454');
        $this->addSql('ALTER TABLE token_manager ADD CONSTRAINT FK_142B3C3A6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_142B3C3A6B9DD454 ON token_manager (user_profile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE token_manager DROP CONSTRAINT FK_142B3C3A6B9DD454');
        $this->addSql('DROP INDEX UNIQ_142B3C3A6B9DD454');
        $this->addSql('ALTER TABLE token_manager DROP uuid');
        $this->addSql('ALTER TABLE token_manager DROP user_profile_id');
    }
}
