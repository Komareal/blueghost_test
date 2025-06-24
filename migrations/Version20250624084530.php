<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624084530 extends AbstractMigration
{

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL
        );
        $this->addSql(<<<'SQL'
            DROP TABLE contact
        SQL
        );
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE contact (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, slug VARCHAR(255) UNIQUE, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, note TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL
        );
    }
}
