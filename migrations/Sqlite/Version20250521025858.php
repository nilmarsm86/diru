<?php

declare(strict_types=1);

namespace DoctrineMigrations\Sqlite;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521025858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, representative_id INTEGER DEFAULT NULL, municipality_id INTEGER NOT NULL, address CLOB NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_C7440455FC3FF006 FOREIGN KEY (representative_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C7440455AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C7440455FC3FF006 ON client (representative_id)');
        $this->addSql('CREATE INDEX IDX_C7440455AE6F181C ON client (municipality_id)');
        $this->addSql('CREATE TABLE constructor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX constructor_name ON constructor (name)');
        $this->addSql('CREATE TABLE contract (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, year INTEGER NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX contract_code ON contract (code)');
        $this->addSql('CREATE TABLE corporate_entity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, organism_id INTEGER NOT NULL, municipality_id INTEGER NOT NULL, code VARCHAR(255) NOT NULL, nit VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_C6EFC8A464180A36 FOREIGN KEY (organism_id) REFERENCES organism (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6EFC8A4AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C6EFC8A464180A36 ON corporate_entity (organism_id)');
        $this->addSql('CREATE INDEX IDX_C6EFC8A4AE6F181C ON corporate_entity (municipality_id)');
        $this->addSql('CREATE UNIQUE INDEX corporate_entity_name ON corporate_entity (name)');
        $this->addSql('CREATE TABLE enterprise_client (id INTEGER NOT NULL, corporate_entity_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_54598E4C8BA692E5 ON enterprise_client (corporate_entity_id)');
        $this->addSql('CREATE TABLE geographic_location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE individual_client (id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_18764BB6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_18764BB6217BBB47 ON individual_client (person_id)');
        $this->addSql('CREATE TABLE investment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, constructor_id INTEGER NOT NULL, location_zone_id INTEGER NOT NULL, municipality_id INTEGER NOT NULL, work_name VARCHAR(255) NOT NULL, investment_name VARCHAR(255) NOT NULL, estimated_value_construction BIGINT NOT NULL, estimated_value_equipment BIGINT NOT NULL, estimated_value_other BIGINT NOT NULL, approved_value_construction BIGINT DEFAULT NULL, approved_value_equipment BIGINT DEFAULT NULL, approved_value_other BIGINT DEFAULT NULL, between_streets CLOB DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, popular_council VARCHAR(255) DEFAULT NULL, block VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, address_number VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_43CA0AD62D98BF9 FOREIGN KEY (constructor_id) REFERENCES constructor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_43CA0AD671692C0F FOREIGN KEY (location_zone_id) REFERENCES location_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_43CA0AD6AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_43CA0AD62D98BF9 ON investment (constructor_id)');
        $this->addSql('CREATE INDEX IDX_43CA0AD671692C0F ON investment (location_zone_id)');
        $this->addSql('CREATE INDEX IDX_43CA0AD6AE6F181C ON investment (municipality_id)');
        $this->addSql('CREATE TABLE location_zone (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE municipality (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, province_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C6F56628E946114A ON municipality (province_id)');
        $this->addSql('CREATE TABLE organism (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX organism_name ON organism (name)');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lastname VARCHAR(255) NOT NULL, identification_number VARCHAR(255) NOT NULL, passport VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX person_identification_number ON person (identification_number)');
        $this->addSql('CREATE UNIQUE INDEX person_passport ON person (passport)');
        $this->addSql('CREATE TABLE province (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX province_name ON province (name)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, importance INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX role_name ON role (name)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, person_id INTEGER NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, CONSTRAINT FK_8D93D649217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649217BBB47 ON "user" (person_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON "user" (username)');
        $this->addSql('CREATE TABLE user_role (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id), CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE constructor');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE corporate_entity');
        $this->addSql('DROP TABLE enterprise_client');
        $this->addSql('DROP TABLE geographic_location');
        $this->addSql('DROP TABLE individual_client');
        $this->addSql('DROP TABLE investment');
        $this->addSql('DROP TABLE location_zone');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE organism');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_role');
    }
}
