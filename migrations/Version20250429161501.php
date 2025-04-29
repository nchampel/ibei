<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429161501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pot DROP INDEX UNIQ_1EBD730FA76ED395, ADD INDEX IDX_1EBD730FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE user DROP money');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pot DROP INDEX IDX_1EBD730FA76ED395, ADD UNIQUE INDEX UNIQ_1EBD730FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE user ADD money BIGINT NOT NULL');
    }
}
