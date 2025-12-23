<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215160731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date_planned DATETIME NOT NULL, end_date_real DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, room_id INT NOT NULL, INDEX IDX_2F84F8E954177093 (room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, room_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_42C8495554177093 (room_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, price_per_night DOUBLE PRECISION NOT NULL, hotel_id INT NOT NULL, INDEX IDX_729F519B3243BB18 (hotel_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE room_service (room_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_DBF263254177093 (room_id), INDEX IDX_DBF2632ED5CA9E6 (service_id), PRIMARY KEY (room_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E954177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495554177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('ALTER TABLE room_service ADD CONSTRAINT FK_DBF263254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_service ADD CONSTRAINT FK_DBF2632ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
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
