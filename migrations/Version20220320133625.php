<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220320133625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_bank (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(10) NOT NULL, name VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_bank_account (id INT AUTO_INCREMENT NOT NULL, app_user_id_id INT NOT NULL, app_bank_id_id INT NOT NULL, account_name VARCHAR(70) NOT NULL, agency VARCHAR(5) NOT NULL, agency_digit VARCHAR(1) NOT NULL, account_number VARCHAR(13) NOT NULL, account_digit VARCHAR(1) NOT NULL, account_type VARCHAR(2) NOT NULL, INDEX IDX_912C248BCB9DCE73 (app_user_id_id), INDEX IDX_912C248B23F5FB5E (app_bank_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, cpf VARCHAR(11) NOT NULL, email VARCHAR(60) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_bank_account ADD CONSTRAINT FK_912C248BCB9DCE73 FOREIGN KEY (app_user_id_id) REFERENCES app_user (id)');
        $this->addSql('ALTER TABLE app_bank_account ADD CONSTRAINT FK_912C248B23F5FB5E FOREIGN KEY (app_bank_id_id) REFERENCES app_bank (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_bank_account DROP FOREIGN KEY FK_912C248B23F5FB5E');
        $this->addSql('ALTER TABLE app_bank_account DROP FOREIGN KEY FK_912C248BCB9DCE73');
        $this->addSql('DROP TABLE app_bank');
        $this->addSql('DROP TABLE app_bank_account');
        $this->addSql('DROP TABLE app_user');
    }
}
