<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710161025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchase AS SELECT id FROM purchase');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('CREATE TABLE purchase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, game_id INTEGER NOT NULL, session_id VARCHAR(255) NOT NULL, purchase_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , price DOUBLE PRECISION NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_6117D13BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO purchase (id) SELECT id FROM __temp__purchase');
        $this->addSql('DROP TABLE __temp__purchase');
        $this->addSql('CREATE INDEX IDX_6117D13BE48FD905 ON purchase (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchase AS SELECT id FROM purchase');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('CREATE TABLE purchase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)');
        $this->addSql('INSERT INTO purchase (id) SELECT id FROM __temp__purchase');
        $this->addSql('DROP TABLE __temp__purchase');
    }
}
