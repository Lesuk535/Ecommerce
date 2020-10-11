<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200922125036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ALTER status TYPE UUID');
        $this->addSql('ALTER TABLE category ALTER status DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN category.status IS \'(DC2Type:category_status)\'');
        $this->addSql('ALTER TABLE product_products ALTER status TYPE UUID');
        $this->addSql('ALTER TABLE product_products ALTER status DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN product_products.status IS \'(DC2Type:product_status)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product_products ALTER status TYPE UUID');
        $this->addSql('ALTER TABLE product_products ALTER status DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN product_products.status IS \'(DC2Type:product_id)\'');
        $this->addSql('ALTER TABLE category ALTER status TYPE UUID');
        $this->addSql('ALTER TABLE category ALTER status DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN category.status IS \'(DC2Type:category_id)\'');
    }
}
