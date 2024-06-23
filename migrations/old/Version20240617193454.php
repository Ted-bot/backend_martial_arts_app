<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617193454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_profile_post_event (user_profile_id INT NOT NULL, post_event_id INT NOT NULL, PRIMARY KEY(user_profile_id, post_event_id))');
        $this->addSql('CREATE INDEX IDX_C79A57EE6B9DD454 ON user_profile_post_event (user_profile_id)');
        $this->addSql('CREATE INDEX IDX_C79A57EECE1A612A ON user_profile_post_event (post_event_id)');
        $this->addSql('ALTER TABLE user_profile_post_event ADD CONSTRAINT FK_C79A57EE6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_profile_post_event ADD CONSTRAINT FK_C79A57EECE1A612A FOREIGN KEY (post_event_id) REFERENCES post_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_profile_post_event DROP CONSTRAINT FK_C79A57EE6B9DD454');
        $this->addSql('ALTER TABLE user_profile_post_event DROP CONSTRAINT FK_C79A57EECE1A612A');
        $this->addSql('DROP TABLE user_profile_post_event');
    }
}
