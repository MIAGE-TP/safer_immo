<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221229103013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fav (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, good_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_769BE06FA76ED395 (user_id), INDEX IDX_769BE06F1CF98C70 (good_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fav ADD CONSTRAINT FK_769BE06FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fav ADD CONSTRAINT FK_769BE06F1CF98C70 FOREIGN KEY (good_id) REFERENCES good (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fav DROP FOREIGN KEY FK_769BE06FA76ED395');
        $this->addSql('ALTER TABLE fav DROP FOREIGN KEY FK_769BE06F1CF98C70');
        $this->addSql('DROP TABLE fav');
    }
}
