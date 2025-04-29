<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429170101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forest_resource (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, forest_resource_id INT NOT NULL, claimed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', cooldown INT NOT NULL, gain INT NOT NULL, x INT DEFAULT NULL, y INT DEFAULT NULL, INDEX IDX_EB3DCF36A76ED395 (user_id), INDEX IDX_EB3DCF3652ADDB69 (forest_resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forest_resource ADD CONSTRAINT FK_EB3DCF36A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE forest_resource ADD CONSTRAINT FK_EB3DCF3652ADDB69 FOREIGN KEY (forest_resource_id) REFERENCES forest_resource_infos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forest_resource DROP FOREIGN KEY FK_EB3DCF36A76ED395');
        $this->addSql('ALTER TABLE forest_resource DROP FOREIGN KEY FK_EB3DCF3652ADDB69');
        $this->addSql('DROP TABLE forest_resource');
    }
}
