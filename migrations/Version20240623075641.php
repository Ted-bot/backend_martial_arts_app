<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240623075641 extends AbstractMigration
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
            WHERE table_name='post_event_user_profile';
                IF NOT FOUND THEN
                    CREATE TABLE post_event_user_profile (post_event_id INT NOT NULL, user_profile_id INT NOT NULL, PRIMARY KEY(post_event_id, user_profile_id));
                    CREATE INDEX IDX_76CD66F4CE1A612A ON post_event_user_profile (post_event_id);
                    CREATE INDEX IDX_76CD66F46B9DD454 ON post_event_user_profile (user_profile_id);
                    ALTER TABLE post_event_user_profile ADD CONSTRAINT FK_76CD66F4CE1A612A FOREIGN KEY (post_event_id) REFERENCES post_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
                    ALTER TABLE post_event_user_profile ADD CONSTRAINT FK_76CD66F46B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
                END IF;
        END;
        $$ language PLPGSQL"
        );
        // $this->addSql('CREATE TABLE post_event_user_profile (post_event_id INT NOT NULL, user_profile_id INT NOT NULL, PRIMARY KEY(post_event_id, user_profile_id))');
        // $this->addSql('CREATE INDEX IDX_76CD66F4CE1A612A ON post_event_user_profile (post_event_id)');
        // $this->addSql('CREATE INDEX IDX_76CD66F46B9DD454 ON post_event_user_profile (user_profile_id)');
        // $this->addSql('ALTER TABLE post_event_user_profile ADD CONSTRAINT FK_76CD66F4CE1A612A FOREIGN KEY (post_event_id) REFERENCES post_event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE post_event_user_profile ADD CONSTRAINT FK_76CD66F46B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post_event_user_profile DROP CONSTRAINT FK_76CD66F4CE1A612A');
        $this->addSql('ALTER TABLE post_event_user_profile DROP CONSTRAINT FK_76CD66F46B9DD454');
        $this->addSql('DROP TABLE post_event_user_profile');
    }
}
