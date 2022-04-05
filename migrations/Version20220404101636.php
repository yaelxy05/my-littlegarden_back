<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404101636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plant ADD potager_id INT NOT NULL');
        $this->addSql('ALTER TABLE plant ADD CONSTRAINT FK_AB030D728FD7C84A FOREIGN KEY (potager_id) REFERENCES potager (id)');
        $this->addSql('CREATE INDEX IDX_AB030D728FD7C84A ON plant (potager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plant DROP FOREIGN KEY FK_AB030D728FD7C84A');
        $this->addSql('DROP INDEX IDX_AB030D728FD7C84A ON plant');
        $this->addSql('ALTER TABLE plant DROP potager_id');
    }
}
