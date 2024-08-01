<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424215325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account_wallet ALTER account_data DROP NOT NULL');
        $this->addSql('ALTER TABLE nft DROP imported_on');
        $this->addSql('ALTER TABLE nft ALTER name DROP NOT NULL');
        $this->addSql('ALTER TABLE nft ALTER description DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE nft ADD imported_on TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE nft ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE nft ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE account_wallet ALTER account_data SET NOT NULL');
    }
}
