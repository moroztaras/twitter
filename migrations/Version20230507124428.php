<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507124428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE twitter_comment (id INT AUTO_INCREMENT NOT NULL, twitter_id INT DEFAULT NULL, user_id INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, approved TINYINT(1) NOT NULL, uuid VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_5805A6FED17F50A6 (uuid), INDEX IDX_5805A6FEC63E6FFF (twitter_id), INDEX IDX_5805A6FEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE twitter_comment ADD CONSTRAINT FK_5805A6FEC63E6FFF FOREIGN KEY (twitter_id) REFERENCES twitter (id)');
        $this->addSql('ALTER TABLE twitter_comment ADD CONSTRAINT FK_5805A6FEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE twitter_comment DROP FOREIGN KEY FK_5805A6FEC63E6FFF');
        $this->addSql('ALTER TABLE twitter_comment DROP FOREIGN KEY FK_5805A6FEA76ED395');
        $this->addSql('DROP TABLE twitter_comment');
    }
}
