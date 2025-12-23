<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215154658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hotel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE maintenance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date_planned DATETIME NOT NULL, end_date_real DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, room_id INT NOT NULL, FOREIGN KEY (room_id) REFERENCES room (id))');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, room_id INT NOT NULL, user_id INT NOT NULL, FOREIGN KEY (room_id) REFERENCES room (id), FOREIGN KEY (user_id) REFERENCES user (id))');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, number INT NOT NULL, price_per_night DOUBLE PRECISION NOT NULL, hotel_id INT NOT NULL, FOREIGN KEY (hotel_id) REFERENCES hotel (id))');
        $this->addSql('CREATE TABLE room_service (room_id INT NOT NULL, service_id INT NOT NULL, FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE, FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE, PRIMARY KEY (room_id, service_id))');
        $this->addSql('CREATE TABLE service (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2F84F8E954177093 ON maintenance (room_id)');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_729F519B3243BB18 ON room (hotel_id)');
        $this->addSql('CREATE INDEX IDX_DBF263254177093 ON room_service (room_id)');
        $this->addSql('CREATE INDEX IDX_DBF2632ED5CA9E6 ON room_service (service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E954177093');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495554177093');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B3243BB18');
        $this->addSql('ALTER TABLE room_service DROP FOREIGN KEY FK_DBF263254177093');
        $this->addSql('ALTER TABLE room_service DROP FOREIGN KEY FK_DBF2632ED5CA9E6');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE maintenance');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_service');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
    }
}
