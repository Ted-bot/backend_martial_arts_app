<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926164104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'deleted OneToOne field and related constraint/ index keys and replaced with (subscription) ManyToOne (user),
         also changed subscribed_to_product to subscribed_product';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT fk_a3c664d35d5e8040');
        $this->addSql('DROP INDEX IF EXISTS uniq_a3c664d35d5e8040');
        $this->addSql('ALTER TABLE subscription ADD IF NOT EXISTS subscribed_product_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP IF EXISTS subscribed_to_product_id');
        $this->addSql('ALTER TABLE subscription ADD IF NOT EXISTS subscription_owned_by_id SET NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT IF EXISTS FK_A3C664D39ECFF961');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39ECFF961 FOREIGN KEY (subscribed_product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_A3C664D39ECFF961 ON subscription (subscribed_product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D39ECFF961');
        $this->addSql('DROP INDEX IDX_A3C664D39ECFF961');
        $this->addSql('ALTER TABLE subscription ADD subscribed_to_product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription DROP subscribed_product_id');
        $this->addSql('ALTER TABLE subscription ALTER subscription_owned_by_id DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT fk_a3c664d35d5e8040 FOREIGN KEY (subscribed_to_product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_a3c664d35d5e8040 ON subscription (subscribed_to_product_id)');
    }
}
