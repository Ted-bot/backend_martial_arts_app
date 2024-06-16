<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602181750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_event ADD IF NOT EXISTS event_date DATE NOT NULL');
        $this->addSql('ALTER TABLE post_event ADD IF NOT EXISTS event_start TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE post_event ADD IF NOT EXISTS event_end TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE post_event ADD IF NOT EXISTS event_regular BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post_event DROP event_date');
        $this->addSql('ALTER TABLE post_event DROP event_start');
        $this->addSql('ALTER TABLE post_event DROP event_end');
        $this->addSql('ALTER TABLE post_event DROP event_regular');
    }
}
