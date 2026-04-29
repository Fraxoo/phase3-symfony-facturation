<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428124413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, quantity, unit, invoice_id_id FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(15, 2) NOT NULL, quantity INTEGER DEFAULT NULL, unit VARCHAR(255) NOT NULL, invoice_id_id INTEGER DEFAULT NULL, CONSTRAINT FK_D34A04AD429ECEE2 FOREIGN KEY (invoice_id_id) REFERENCES invoice (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product (id, name, description, price, quantity, unit, invoice_id_id) SELECT id, name, description, price, quantity, unit, invoice_id_id FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE INDEX IDX_D34A04AD429ECEE2 ON product (invoice_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, quantity, unit, invoice_id_id FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(15, 2) NOT NULL, quantity INTEGER DEFAULT NULL, unit VARCHAR(255) NOT NULL, invoice_id_id INTEGER NOT NULL, CONSTRAINT FK_D34A04AD429ECEE2 FOREIGN KEY (invoice_id_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product (id, name, description, price, quantity, unit, invoice_id_id) SELECT id, name, description, price, quantity, unit, invoice_id_id FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE INDEX IDX_D34A04AD429ECEE2 ON product (invoice_id_id)');
    }
}
