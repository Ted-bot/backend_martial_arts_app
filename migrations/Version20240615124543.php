<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240615124543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("do $$
        BEGIN 
            perform table_name FROM information_schema.tables
            WHERE table_name='post_event';
                IF NOT FOUND THEN
                    ALTER TABLE post_event ADD start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL;
                    ALTER TABLE post_event ADD end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL;
                    ALTER TABLE post_event ADD all_day BOOLEAN DEFAULT NULL;
                END IF;
        END;
        $$ language PLPGSQL"
        );
        // $this->addSql('ALTER TABLE post_event ADD start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        // $this->addSql('ALTER TABLE post_event ADD end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        // $this->addSql('ALTER TABLE post_event ADD all_day BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post_event DROP start_date');
        $this->addSql('ALTER TABLE post_event DROP end_date');
        $this->addSql('ALTER TABLE post_event DROP all_day');
    }
}
