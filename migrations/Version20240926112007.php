<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926112007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'note: delete migration if see fit: (re)created subscribed_to_product';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription ADD IF NOT EXISTS subscribed_to_product_id INT DEFAULT NULL');
        
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT IF EXISTS FK_A3C664D35D5E8040');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D35D5E8040 FOREIGN KEY (subscribed_to_product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // $this->addSql('ALTER TABLE subscription ADD CONSTRAINT IF NOT EXISTS FK_A3C664D35D5E8040 FOREIGN KEY (subscribed_to_product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_A3C664D35D5E8040 ON subscription (subscribed_to_product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D35D5E8040');
        $this->addSql('DROP INDEX UNIQ_A3C664D35D5E8040');
        $this->addSql('ALTER TABLE subscription DROP subscribed_to_product_id');
    }
}
