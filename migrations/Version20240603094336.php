<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603094336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("do $$
        BEGIN 
            perform conrelid::regclass AS table_name, conname AS foreignKey, pg_get_constraintdef(oid) 
            FROM pg_constraint 
            WHERE contype = 'f' AND connamespace = 'public'::regnamespace AND pg_get_constraintdef(oid) LIKE '%group_student_id%';
                IF NOT FOUND THEN
                    ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4051C592EA8 FOREIGN KEY (user_id) REFERENCES \"user\" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
                END IF;
        END;
        $$ language PLPGSQL"
        );
        // $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4051C592EA8 FOREIGN KEY (user_id) REFERENCES \"user\" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        // $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4051C592EA8 FOREIGN KEY (user_id) REFERENCES \"user\" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_profile DROP CONSTRAINT FK_D95AB4051C592EA8');
        // $this->addSql('DROP TABLE password_token');
    }
}
