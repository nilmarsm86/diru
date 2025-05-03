<?php

declare(strict_types=1);

namespace DoctrineMigrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423221052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, municipality_id INT NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C7440455217BBB47 (person_id), INDEX IDX_C7440455AE6F181C (municipality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE corporate_entity (id INT AUTO_INCREMENT NOT NULL, municipality_id INT NOT NULL, organism_id INT NOT NULL, code VARCHAR(255) NOT NULL, nit VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C6EFC8A4AE6F181C (municipality_id), INDEX IDX_C6EFC8A464180A36 (organism_id), UNIQUE INDEX corporate_entity_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enterprise_client (id INT NOT NULL, corporate_entity_id INT NOT NULL, active_contract TINYINT(1) NOT NULL, INDEX IDX_54598E4C8BA692E5 (corporate_entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE individual_client (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipality (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C6F56628E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organism (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX organism_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, identification_number VARCHAR(255) NOT NULL, passport VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX person_identification_number (identification_number), UNIQUE INDEX person_passport (passport), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX province_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, importance INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX role_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, identification_number VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE corporate_entity ADD CONSTRAINT FK_C6EFC8A4AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE corporate_entity ADD CONSTRAINT FK_C6EFC8A464180A36 FOREIGN KEY (organism_id) REFERENCES organism (id)');
        $this->addSql('ALTER TABLE enterprise_client ADD CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id)');
        $this->addSql('ALTER TABLE enterprise_client ADD CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE individual_client ADD CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455217BBB47');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455AE6F181C');
        $this->addSql('ALTER TABLE corporate_entity DROP FOREIGN KEY FK_C6EFC8A4AE6F181C');
        $this->addSql('ALTER TABLE corporate_entity DROP FOREIGN KEY FK_C6EFC8A464180A36');
        $this->addSql('ALTER TABLE enterprise_client DROP FOREIGN KEY FK_54598E4C8BA692E5');
        $this->addSql('ALTER TABLE enterprise_client DROP FOREIGN KEY FK_54598E4CBF396750');
        $this->addSql('ALTER TABLE individual_client DROP FOREIGN KEY FK_18764BB6BF396750');
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F56628E946114A');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE corporate_entity');
        $this->addSql('DROP TABLE enterprise_client');
        $this->addSql('DROP TABLE individual_client');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE organism');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_role');
    }
}
