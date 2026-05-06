<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505123745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantity INTEGER NOT NULL, product_id_id INTEGER NOT NULL, invoice_id_id INTEGER NOT NULL, CONSTRAINT FK_1DDE477BDE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1DDE477B429ECEE2 FOREIGN KEY (invoice_id_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1DDE477BDE18E50B ON invoice_item (product_id_id)');
        $this->addSql('CREATE INDEX IDX_1DDE477B429ECEE2 ON invoice_item (invoice_id_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, name, email, phone, adress, siret, rib, user_id_id FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, siret VARCHAR(255) DEFAULT NULL, rib VARCHAR(255) NOT NULL, user_id_id INTEGER NOT NULL, CONSTRAINT FK_C74404559D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO client (id, name, email, phone, adress, siret, rib, user_id_id) SELECT id, name, email, phone, adress, siret, rib, user_id_id FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE INDEX IDX_C74404559D86650F ON client (user_id_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, unit FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(15, 2) NOT NULL, unit VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO product (id, name, description, price, unit) SELECT id, name, description, price, unit FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, name, email, phone, adress, siret, rib, user_id_id FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, siret VARCHAR(255) NOT NULL, rib VARCHAR(255) NOT NULL, user_id_id INTEGER NOT NULL, CONSTRAINT FK_C74404559D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO client (id, name, email, phone, adress, siret, rib, user_id_id) SELECT id, name, email, phone, adress, siret, rib, user_id_id FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE INDEX IDX_C74404559D86650F ON client (user_id_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, name, description, price, unit FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(15, 2) NOT NULL, unit VARCHAR(255) NOT NULL, quantity INTEGER DEFAULT NULL, invoice_id_id INTEGER DEFAULT NULL, CONSTRAINT FK_D34A04AD429ECEE2 FOREIGN KEY (invoice_id_id) REFERENCES invoice (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product (id, name, description, price, unit) SELECT id, name, description, price, unit FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
        $this->addSql('CREATE INDEX IDX_D34A04AD429ECEE2 ON product (invoice_id_id)');
    }
}
