<?php

declare(strict_types=1);

namespace DoctrineMigrations\Sqlite;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250821150600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE building (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, project_id INTEGER DEFAULT NULL, land_id INTEGER DEFAULT NULL, state VARCHAR(255) NOT NULL, stop_reason CLOB DEFAULT NULL, estimated_value_construction BIGINT NOT NULL, estimated_value_equipment BIGINT NOT NULL, estimated_value_other BIGINT NOT NULL, approved_value_construction BIGINT NOT NULL, approved_value_equipment BIGINT NOT NULL, approved_value_other BIGINT NOT NULL, is_new BOOLEAN DEFAULT NULL, name VARCHAR(255) NOT NULL, has_reply BOOLEAN DEFAULT NULL, CONSTRAINT FK_E16F61D4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E16F61D41994904A FOREIGN KEY (land_id) REFERENCES land (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E16F61D4166D1F9C ON building (project_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E16F61D41994904A ON building (land_id)');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, representative_id INTEGER DEFAULT NULL, municipality_id INTEGER NOT NULL, address CLOB NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_C7440455FC3FF006 FOREIGN KEY (representative_id) REFERENCES representative (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C7440455AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C7440455FC3FF006 ON client (representative_id)');
        $this->addSql('CREATE INDEX IDX_C7440455AE6F181C ON client (municipality_id)');
        $this->addSql('CREATE TABLE constructive_action (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE constructor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX constructor_name ON constructor (name)');
        $this->addSql('CREATE TABLE constructor_building (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, constructor_id INTEGER NOT NULL, building_id INTEGER NOT NULL, started_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , finished_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_29A127572D98BF9 FOREIGN KEY (constructor_id) REFERENCES constructor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_29A127574D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_29A127572D98BF9 ON constructor_building (constructor_id)');
        $this->addSql('CREATE INDEX IDX_29A127574D2A7E12 ON constructor_building (building_id)');
        $this->addSql('CREATE TABLE contract (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, year INTEGER NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX contract_code ON contract (code)');
        $this->addSql('CREATE TABLE corporate_entity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, organism_id INTEGER NOT NULL, municipality_id INTEGER NOT NULL, code VARCHAR(255) NOT NULL, nit VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_C6EFC8A464180A36 FOREIGN KEY (organism_id) REFERENCES organism (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6EFC8A4AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C6EFC8A464180A36 ON corporate_entity (organism_id)');
        $this->addSql('CREATE INDEX IDX_C6EFC8A4AE6F181C ON corporate_entity (municipality_id)');
        $this->addSql('CREATE UNIQUE INDEX corporate_entity_name ON corporate_entity (name)');
        $this->addSql('CREATE TABLE currency (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE draftsman (id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_19A4FE4ABF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE draftsman_building (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, draftsman_id INTEGER NOT NULL, building_id INTEGER NOT NULL, started_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , finished_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_F9CF0F7B40C866FD FOREIGN KEY (draftsman_id) REFERENCES draftsman (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F9CF0F7B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F9CF0F7B40C866FD ON draftsman_building (draftsman_id)');
        $this->addSql('CREATE INDEX IDX_F9CF0F7B4D2A7E12 ON draftsman_building (building_id)');
        $this->addSql('CREATE TABLE enterprise_client (id INTEGER NOT NULL, corporate_entity_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_54598E4C8BA692E5 ON enterprise_client (corporate_entity_id)');
        $this->addSql('CREATE TABLE floor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building_id INTEGER NOT NULL, original_id INTEGER DEFAULT NULL, ground_floor BOOLEAN NOT NULL, position INTEGER NOT NULL, name VARCHAR(255) NOT NULL, has_reply BOOLEAN DEFAULT NULL, CONSTRAINT FK_BE45D62E4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BE45D62E108B7592 FOREIGN KEY (original_id) REFERENCES floor (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BE45D62E4D2A7E12 ON floor (building_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE45D62E108B7592 ON floor (original_id)');
        $this->addSql('CREATE TABLE geographic_location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE individual_client (id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_18764BB6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_18764BB6217BBB47 ON individual_client (person_id)');
        $this->addSql('CREATE TABLE investment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, location_zone_id INTEGER DEFAULT NULL, municipality_id INTEGER NOT NULL, between_streets CLOB DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, popular_council VARCHAR(255) DEFAULT NULL, block VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, address_number VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_43CA0AD671692C0F FOREIGN KEY (location_zone_id) REFERENCES location_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_43CA0AD6AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_43CA0AD671692C0F ON investment (location_zone_id)');
        $this->addSql('CREATE INDEX IDX_43CA0AD6AE6F181C ON investment (municipality_id)');
        $this->addSql('CREATE TABLE land (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, land_area INTEGER NOT NULL, occupied_area INTEGER NOT NULL, perimeter INTEGER DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, microlocalization VARCHAR(255) DEFAULT NULL, floor INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE land_network_connection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, land_id INTEGER NOT NULL, network_connection_id INTEGER NOT NULL, explanation CLOB DEFAULT NULL, CONSTRAINT FK_7BF47B3B1994904A FOREIGN KEY (land_id) REFERENCES land (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7BF47B3B4EDCA4BC FOREIGN KEY (network_connection_id) REFERENCES network_connection (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7BF47B3B1994904A ON land_network_connection (land_id)');
        $this->addSql('CREATE INDEX IDX_7BF47B3B4EDCA4BC ON land_network_connection (network_connection_id)');
        $this->addSql('CREATE TABLE local (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, sub_system_id INTEGER NOT NULL, local_constructive_action_id INTEGER DEFAULT NULL, original_id INTEGER DEFAULT NULL, number INTEGER NOT NULL, area INTEGER NOT NULL, type VARCHAR(255) NOT NULL, height DOUBLE PRECISION NOT NULL, technical_status VARCHAR(255) NOT NULL, impact_higher_levels BOOLEAN NOT NULL, comment CLOB DEFAULT NULL, name VARCHAR(255) NOT NULL, has_reply BOOLEAN DEFAULT NULL, state VARCHAR(255) NOT NULL, CONSTRAINT FK_8BD688E8C298691B FOREIGN KEY (sub_system_id) REFERENCES sub_system (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8BD688E8460C8FAC FOREIGN KEY (local_constructive_action_id) REFERENCES local_constructive_action (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8BD688E8108B7592 FOREIGN KEY (original_id) REFERENCES local (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8BD688E8C298691B ON local (sub_system_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BD688E8460C8FAC ON local (local_constructive_action_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BD688E8108B7592 ON local (original_id)');
        $this->addSql('CREATE TABLE local_constructive_action (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, constructive_action_id INTEGER DEFAULT NULL, price BIGINT NOT NULL, CONSTRAINT FK_EECA25F5369941F1 FOREIGN KEY (constructive_action_id) REFERENCES constructive_action (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EECA25F5369941F1 ON local_constructive_action (constructive_action_id)');
        $this->addSql('CREATE TABLE location_zone (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE municipality (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, province_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C6F56628E946114A ON municipality (province_id)');
        $this->addSql('CREATE TABLE network_connection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE organism (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX organism_name ON organism (name)');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lastname VARCHAR(255) NOT NULL, identification_number VARCHAR(255) NOT NULL, passport VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, discr VARCHAR(255) DEFAULT \'other\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX person_identification_number ON person (identification_number)');
        $this->addSql('CREATE UNIQUE INDEX person_passport ON person (passport)');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER DEFAULT NULL, contract_id INTEGER DEFAULT NULL, investment_id INTEGER NOT NULL, currency_id INTEGER NOT NULL, type VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, stop_reason CLOB DEFAULT NULL, register_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , stopped_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , canceled_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , initiated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , terrain_diagnosis_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , urban_regulation_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , design_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , comment CLOB DEFAULT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT FK_2FB3D0EE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EE2576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EE6E1B4FD5 FOREIGN KEY (investment_id) REFERENCES investment (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EE38248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE19EB6921 ON project (client_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE2576E0FD ON project (contract_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE6E1B4FD5 ON project (investment_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE38248176 ON project (currency_id)');
        $this->addSql('CREATE TABLE province (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX province_name ON province (name)');
        $this->addSql('CREATE TABLE representative (id INTEGER NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2507390EBF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, importance INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX role_name ON role (name)');
        $this->addSql('CREATE TABLE sub_system (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, floor_id INTEGER NOT NULL, original_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, has_reply BOOLEAN DEFAULT NULL, CONSTRAINT FK_7B1C5EC7854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7B1C5EC7108B7592 FOREIGN KEY (original_id) REFERENCES sub_system (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7B1C5EC7854679E2 ON sub_system (floor_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B1C5EC7108B7592 ON sub_system (original_id)');
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
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE constructive_action');
        $this->addSql('DROP TABLE constructor');
        $this->addSql('DROP TABLE constructor_building');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE corporate_entity');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE draftsman');
        $this->addSql('DROP TABLE draftsman_building');
        $this->addSql('DROP TABLE enterprise_client');
        $this->addSql('DROP TABLE floor');
        $this->addSql('DROP TABLE geographic_location');
        $this->addSql('DROP TABLE individual_client');
        $this->addSql('DROP TABLE investment');
        $this->addSql('DROP TABLE land');
        $this->addSql('DROP TABLE land_network_connection');
        $this->addSql('DROP TABLE local');
        $this->addSql('DROP TABLE local_constructive_action');
        $this->addSql('DROP TABLE location_zone');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE network_connection');
        $this->addSql('DROP TABLE organism');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE representative');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE sub_system');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_role');
    }
}
