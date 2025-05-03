<?php

declare(strict_types=1);

namespace DoctrineMigrations\Sqlite;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423220548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, person_id INTEGER NOT NULL, municipality_id INTEGER NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address CLOB NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_C7440455217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C7440455AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455217BBB47 ON client (person_id)');
        $this->addSql('CREATE INDEX IDX_C7440455AE6F181C ON client (municipality_id)');
        $this->addSql('CREATE TABLE corporate_entity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, municipality_id INTEGER NOT NULL, organism_id INTEGER NOT NULL, code VARCHAR(255) NOT NULL, nit VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_C6EFC8A4AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6EFC8A464180A36 FOREIGN KEY (organism_id) REFERENCES organism (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C6EFC8A4AE6F181C ON corporate_entity (municipality_id)');
        $this->addSql('CREATE INDEX IDX_C6EFC8A464180A36 ON corporate_entity (organism_id)');
        $this->addSql('CREATE UNIQUE INDEX corporate_entity_name ON corporate_entity (name)');
        $this->addSql('CREATE TABLE enterprise_client (id INTEGER NOT NULL, corporate_entity_id INTEGER NOT NULL, active_contract BOOLEAN NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_54598E4C8BA692E5 ON enterprise_client (corporate_entity_id)');
        $this->addSql('CREATE TABLE individual_client (id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE municipality (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, province_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C6F56628E946114A ON municipality (province_id)');
        $this->addSql('CREATE TABLE organism (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX organism_name ON organism (name)');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, identification_number VARCHAR(255) NOT NULL, passport VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX person_identification_number ON person (identification_number)');
        $this->addSql('CREATE UNIQUE INDEX person_passport ON person (passport)');
        $this->addSql('CREATE TABLE province (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX province_name ON province (name)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, importance INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX role_name ON role (name)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, identification_number VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON "user" (username)');
        $this->addSql('CREATE TABLE user_role (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id), CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE corporate_entity');
        $this->addSql('DROP TABLE enterprise_client');
        $this->addSql('DROP TABLE individual_client');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE organism');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_role');
    }
}
