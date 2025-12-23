<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251221170645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hotel ADD address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service ADD description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service ADD price DOUBLE PRECISION NOT NULL');
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
