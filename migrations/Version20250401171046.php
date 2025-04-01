<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401171046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE tblProductData (intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL, strProductName VARCHAR(50) NOT NULL, strProductDesc VARCHAR(255) NOT NULL, strProductCode VARCHAR(10) NOT NULL, dtmAdded DATETIME DEFAULT NULL, dtmDiscontinued DATETIME DEFAULT NULL, stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_2C11248662F10A58 (strProductCode), PRIMARY KEY(intProductDataId)) DEFAULT CHARACTER SET utf8mb4
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE tblProductData
        SQL);
    }
}
