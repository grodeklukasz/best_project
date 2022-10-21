<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221021113522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE termin (id INT AUTO_INCREMENT NOT NULL, tn_id INT NOT NULL, termintype_id INT NOT NULL, termindatum DATE NOT NULL, INDEX IDX_EFAFBA9C88892D00 (tn_id), INDEX IDX_EFAFBA9C2A1EF9E1 (termintype_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_type (id INT AUTO_INCREMENT NOT NULL, termin_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE termin ADD CONSTRAINT FK_EFAFBA9C88892D00 FOREIGN KEY (tn_id) REFERENCES tn (id)');
        $this->addSql('ALTER TABLE termin ADD CONSTRAINT FK_EFAFBA9C2A1EF9E1 FOREIGN KEY (termintype_id) REFERENCES termin_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE termin DROP FOREIGN KEY FK_EFAFBA9C88892D00');
        $this->addSql('ALTER TABLE termin DROP FOREIGN KEY FK_EFAFBA9C2A1EF9E1');
        $this->addSql('DROP TABLE termin');
        $this->addSql('DROP TABLE termin_type');
    }
}
