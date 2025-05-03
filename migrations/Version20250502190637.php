<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502190637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forest_field (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, forest_resource_id INT DEFAULT NULL, x INT NOT NULL, y INT NOT NULL, UNIQUE INDEX UNIQ_C129961BA76ED395 (user_id), INDEX IDX_C129961B52ADDB69 (forest_resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forest_field ADD CONSTRAINT FK_C129961BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE forest_field ADD CONSTRAINT FK_C129961B52ADDB69 FOREIGN KEY (forest_resource_id) REFERENCES forest_resource (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forest_field DROP FOREIGN KEY FK_C129961BA76ED395');
        $this->addSql('ALTER TABLE forest_field DROP FOREIGN KEY FK_C129961B52ADDB69');
        $this->addSql('DROP TABLE forest_field');
    }
}
