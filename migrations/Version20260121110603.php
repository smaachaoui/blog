<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121110603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE update_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE post RENAME INDEX uniq_category_slug TO uniq_post_slug');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE updated_at update_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE post RENAME INDEX uniq_post_slug TO uniq_category_slug');
    }
}
