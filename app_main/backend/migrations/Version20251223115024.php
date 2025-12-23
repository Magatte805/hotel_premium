<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223115024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Rename reserved table name "user" -> users (PostgreSQL)
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c84955a76ed395');
        $this->addSql('ALTER TABLE "user" RENAME TO users');

        // Keep sequence name consistent (optional but cleaner)
        $this->addSql('ALTER SEQUENCE IF EXISTS user_id_seq RENAME TO users_id_seq');

        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c84955a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c84955a76ed395');
        $this->addSql('ALTER TABLE users RENAME TO "user"');
        $this->addSql('ALTER SEQUENCE IF EXISTS users_id_seq RENAME TO user_id_seq');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c84955a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
