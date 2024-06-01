<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507170012 extends AbstractMigration
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
            perform conrelid::regclass AS table_name, conname AS foreignKey, pg_get_constraintdef(oid) 
            FROM pg_constraint 
            WHERE contype = 'f' AND connamespace = 'public'::regnamespace AND pg_get_constraintdef(oid) LIKE '%user_uniq_id%';
                IF NOT FOUND THEN
                    ALTER TABLE user_profile ADD user_uniq_id INT DEFAULT NULL;
                    ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405976E9E35 FOREIGN KEY (user_uniq_id) REFERENCES 'user' (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
                    CREATE UNIQUE INDEX UNIQ_D95AB405976E9E35 ON user_profile (user_uniq_id);
                END IF;
        END;
        $$ language PLPGSQL"); //RAISE NOTICE "Found";
        // $this->addSql('ALTER TABLE user_profile ADD user_uniq_id INT DEFAULT NULL');
        // $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405976E9E35 FOREIGN KEY (user_uniq_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('CREATE UNIQUE INDEX UNIQ_D95AB405976E9E35 ON user_profile (user_uniq_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_profile DROP CONSTRAINT FK_D95AB405976E9E35');
        $this->addSql('DROP INDEX UNIQ_D95AB405976E9E35');
        $this->addSql('ALTER TABLE user_profile DROP user_uniq_id');
    }
}
