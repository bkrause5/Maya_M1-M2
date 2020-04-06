<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version2020010745621 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fonction (noFonction INT AUTO_INCREMENT NOT NULL, libFonction VARCHAR(50) NOT NULL,PRIMARY KEY(noFonction)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employe ADD noFonction_id INT NOT NULL');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_14M8VL36ORG9Q41Y FOREIGN KEY (fonction_noFonction) REFERENCES fonction (noFonction)');
        $this->addSql('CREATE INDEX IDX_14M8VL36ORG9Q41Y ON employe (fonction_noFonction)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_14M8VL36ORG9Q41YD');
        $this->addSql('DROP TABLE fonction');
        $this->addSql('DROP INDEX IDX_14M8VL36ORG9Q41YD ON employe');
        $this->addSql('ALTER TABLE employe DROP fonction_noFonction');
    }
}
