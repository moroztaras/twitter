<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230813085703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE twitter ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE twitter ADD CONSTRAINT FK_166A7BB6727ACA70 FOREIGN KEY (parent_id) REFERENCES twitter (id)');
        $this->addSql('CREATE INDEX IDX_166A7BB6727ACA70 ON twitter (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE twitter DROP FOREIGN KEY FK_166A7BB6727ACA70');
        $this->addSql('DROP INDEX IDX_166A7BB6727ACA70 ON twitter');
        $this->addSql('ALTER TABLE twitter DROP parent_id');
    }
}
