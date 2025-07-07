<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190916151012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mentions');
        $this->addSql('ALTER TABLE orders CHANGE cart cart JSON NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE picture2 picture2 VARCHAR(255) DEFAULT NULL, CHANGE picture3 picture3 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD password VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD address VARCHAR(255) NOT NULL, ADD additional_address VARCHAR(255) NOT NULL, ADD postal_code INT NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD phone_number VARCHAR(255) NOT NULL, ADD reset_token VARCHAR(255) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE roles roles JSON NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mentions (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE orders CHANGE cart cart LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE products CHANGE picture2 picture2 VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE picture3 picture3 VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE users MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('ALTER TABLE users DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE users DROP password, DROP last_name, DROP first_name, DROP address, DROP additional_address, DROP postal_code, DROP city, DROP phone_number, DROP reset_token, CHANGE id id INT NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
