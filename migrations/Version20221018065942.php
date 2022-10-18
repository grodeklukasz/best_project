<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221018065942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fm (id INT AUTO_INCREMENT NOT NULL, nachname VARCHAR(255) NOT NULL, vorname VARCHAR(255) DEFAULT NULL, zw VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jobcoach (id INT AUTO_INCREMENT NOT NULL, nachname VARCHAR(255) NOT NULL, vorname VARCHAR(255) NOT NULL, telefonnummer VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tn (id INT AUTO_INCREMENT NOT NULL, jobcoach_id INT NOT NULL, fm_id INT DEFAULT NULL, nachname VARCHAR(255) NOT NULL, vorname VARCHAR(255) NOT NULL, telefonnummer VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, gebdatum DATE NOT NULL, pseudonym VARCHAR(255) DEFAULT NULL, starttermin DATE DEFAULT NULL, ausgeschieden DATE DEFAULT NULL, grund_ausgeschieden VARCHAR(255) DEFAULT NULL, status TINYINT(1) NOT NULL, bemerkung LONGTEXT DEFAULT NULL, INDEX IDX_A080E252D2B70077 (jobcoach_id), INDEX IDX_A080E252801C460C (fm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tn ADD CONSTRAINT FK_A080E252D2B70077 FOREIGN KEY (jobcoach_id) REFERENCES jobcoach (id)');
        $this->addSql('ALTER TABLE tn ADD CONSTRAINT FK_A080E252801C460C FOREIGN KEY (fm_id) REFERENCES fm (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tn DROP FOREIGN KEY FK_A080E252D2B70077');
        $this->addSql('ALTER TABLE tn DROP FOREIGN KEY FK_A080E252801C460C');
        $this->addSql('DROP TABLE fm');
        $this->addSql('DROP TABLE jobcoach');
        $this->addSql('DROP TABLE tn');
    }
}
