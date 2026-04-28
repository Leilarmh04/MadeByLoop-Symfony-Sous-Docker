<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260424160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sizes and colors JSON columns to product table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD sizes JSON DEFAULT NULL, ADD colors JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP sizes, DROP colors');
    }
}