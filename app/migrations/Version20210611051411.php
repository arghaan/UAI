<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611051411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flight (id SERIAL NOT NULL, status INT NOT NULL, flight_volume INT NOT NULL, secret_key VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ticket (id SERIAL NOT NULL, flight_id INT NOT NULL, place_number INT NOT NULL, status INT NOT NULL, booking_key VARCHAR(255) DEFAULT NULL, purchase_key VARCHAR(255) DEFAULT NULL, customer_email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A0ADA3ACF5927E ON ticket (booking_key)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A0ADA353AC47AC ON ticket (purchase_key)');
        $this->addSql('CREATE INDEX IDX_97A0ADA391F478C5 ON ticket (flight_id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA391F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA391F478C5');
        $this->addSql('DROP TABLE flight');
        $this->addSql('DROP TABLE ticket');
    }
}
