<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250710155544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workshop DROP FOREIGN KEY FK_9B6F02C47E3C61F9');
        $this->addSql('DROP TABLE workshop');
        $this->addSql('ALTER TABLE forest_resource ADD next_available_at DATETIME DEFAULT NULL, DROP cooldown, DROP gain');
        $this->addSql('ALTER TABLE forest_resource_infos ADD harvest_time INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE workshop (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, soldiers INT NOT NULL, product_stock INT NOT NULL, reserve_stock INT NOT NULL, x INT DEFAULT NULL, y INT DEFAULT NULL, cooldown INT NOT NULL, number_product INT NOT NULL, harvested_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9B6F02C47E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE workshop ADD CONSTRAINT FK_9B6F02C47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE forest_resource ADD cooldown INT NOT NULL, ADD gain INT NOT NULL, DROP next_available_at');
        $this->addSql('ALTER TABLE forest_resource_infos DROP harvest_time');
    }
}
