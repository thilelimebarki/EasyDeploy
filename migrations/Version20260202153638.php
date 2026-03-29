<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202153638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    // public function up(Schema $schema): void
    // {
    //     // this up() migration is auto-generated, please modify it to your needs
    //     $this->addSql('ALTER TABLE technicien ADD email_verifie TINYINT(1) DEFAULT NULL');
    // }

    public function up(Schema $schema): void
{
    // 1) Ajout colonne temporairement NULL
    $this->addSql("ALTER TABLE technicien ADD email_verifie TINYINT(1) DEFAULT NULL");

    // 2) On valide TOUS les anciens comptes
    $this->addSql("UPDATE technicien SET email_verifie = 1 WHERE email_verifie IS NULL");

    // 3) Pour les nouveaux: NOT NULL + DEFAULT 0
    $this->addSql("ALTER TABLE technicien MODIFY email_verifie TINYINT(1) NOT NULL DEFAULT 0");
}

    // public function down(Schema $schema): void
    // {
    //     // this down() migration is auto-generated, please modify it to your needs
    //     $this->addSql('ALTER TABLE technicien DROP email_verifie');
    // }

    public function down(Schema $schema): void
{
    $this->addSql("ALTER TABLE technicien DROP email_verifie");
}

}
