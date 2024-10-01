<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926232350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adding token_manager_id to susbcription';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription ADD IF NOT EXISTS token_manager_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D390F225DD');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D390F225DD FOREIGN KEY (token_manager_id) REFERENCES token_manager (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_A3C664D390F225DD ON subscription (token_manager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D390F225DD');
        $this->addSql('DROP INDEX IDX_A3C664D390F225DD');
        $this->addSql('ALTER TABLE subscription DROP token_manager_id');
    }
}
