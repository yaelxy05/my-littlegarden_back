<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220223105126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legume ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE legume ADD CONSTRAINT FK_86667383A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_86667383A76ED395 ON legume (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legume DROP FOREIGN KEY FK_86667383A76ED395');
        $this->addSql('DROP INDEX IDX_86667383A76ED395 ON legume');
        $this->addSql('ALTER TABLE legume DROP user_id');
    }
}
