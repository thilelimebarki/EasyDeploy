<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250830190212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD script_download_path VARCHAR(255) DEFAULT NULL, ADD script_execute_path VARCHAR(255) DEFAULT NULL, DROP script_path, DROP script_command, DROP commande_execution');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application ADD script_path VARCHAR(255) DEFAULT NULL, ADD script_command LONGTEXT DEFAULT NULL, ADD commande_execution VARCHAR(255) DEFAULT NULL, DROP script_download_path, DROP script_execute_path');
    }
}
