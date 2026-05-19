<?php

declare(strict_types=1);

namespace DoctrineMigrations\Sqlite;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519210330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__draftsman AS SELECT id FROM draftsman');
        $this->addSql('DROP TABLE draftsman');
        $this->addSql('CREATE TABLE draftsman (id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_19A4FE4ABF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO draftsman (id) SELECT id FROM __temp__draftsman');
        $this->addSql('DROP TABLE __temp__draftsman');
        $this->addSql('CREATE TEMPORARY TABLE __temp__enterprise_client AS SELECT id, corporate_entity_id FROM enterprise_client');
        $this->addSql('DROP TABLE enterprise_client');
        $this->addSql('CREATE TABLE enterprise_client (id INTEGER NOT NULL, corporate_entity_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO enterprise_client (id, corporate_entity_id) SELECT id, corporate_entity_id FROM __temp__enterprise_client');
        $this->addSql('DROP TABLE __temp__enterprise_client');
        $this->addSql('CREATE INDEX IDX_54598E4C8BA692E5 ON enterprise_client (corporate_entity_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__individual_client AS SELECT id, person_id FROM individual_client');
        $this->addSql('DROP TABLE individual_client');
        $this->addSql('CREATE TABLE individual_client (id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_18764BB6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO individual_client (id, person_id) SELECT id, person_id FROM __temp__individual_client');
        $this->addSql('DROP TABLE __temp__individual_client');
        $this->addSql('CREATE INDEX IDX_18764BB6217BBB47 ON individual_client (person_id)');
        $this->addSql('ALTER TABLE ite ADD COLUMN type VARCHAR(255) NOT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__just_value_estimate AS SELECT id, building_id FROM just_value_estimate');
        $this->addSql('DROP TABLE just_value_estimate');
        $this->addSql('CREATE TABLE just_value_estimate (id INTEGER NOT NULL, building_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_49630CA34D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_49630CA3BF396750 FOREIGN KEY (id) REFERENCES estimate (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO just_value_estimate (id, building_id) SELECT id, building_id FROM __temp__just_value_estimate');
        $this->addSql('DROP TABLE __temp__just_value_estimate');
        $this->addSql('CREATE INDEX IDX_49630CA34D2A7E12 ON just_value_estimate (building_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_technical_preparation_estimate AS SELECT id, building_id FROM project_technical_preparation_estimate');
        $this->addSql('DROP TABLE project_technical_preparation_estimate');
        $this->addSql('CREATE TABLE project_technical_preparation_estimate (id INTEGER NOT NULL, building_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_39DA26B14D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_39DA26B1BF396750 FOREIGN KEY (id) REFERENCES estimate (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_technical_preparation_estimate (id, building_id) SELECT id, building_id FROM __temp__project_technical_preparation_estimate');
        $this->addSql('DROP TABLE __temp__project_technical_preparation_estimate');
        $this->addSql('CREATE INDEX IDX_39DA26B14D2A7E12 ON project_technical_preparation_estimate (building_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__representative AS SELECT id, phone, email FROM representative');
        $this->addSql('DROP TABLE representative');
        $this->addSql('CREATE TABLE representative (id INTEGER NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2507390EBF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO representative (id, phone, email) SELECT id, phone, email FROM __temp__representative');
        $this->addSql('DROP TABLE __temp__representative');
        $this->addSql('CREATE TEMPORARY TABLE __temp__urbanization_estimate AS SELECT id, building_id FROM urbanization_estimate');
        $this->addSql('DROP TABLE urbanization_estimate');
        $this->addSql('CREATE TABLE urbanization_estimate (id INTEGER NOT NULL, building_id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_89AA13F84D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_89AA13F8BF396750 FOREIGN KEY (id) REFERENCES estimate (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO urbanization_estimate (id, building_id) SELECT id, building_id FROM __temp__urbanization_estimate');
        $this->addSql('DROP TABLE __temp__urbanization_estimate');
        $this->addSql('CREATE INDEX IDX_89AA13F84D2A7E12 ON urbanization_estimate (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__draftsman AS SELECT id FROM draftsman');
        $this->addSql('DROP TABLE draftsman');
        $this->addSql('CREATE TABLE draftsman (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_19A4FE4ABF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO draftsman (id) SELECT id FROM __temp__draftsman');
        $this->addSql('DROP TABLE __temp__draftsman');
        $this->addSql('CREATE TEMPORARY TABLE __temp__enterprise_client AS SELECT id, corporate_entity_id FROM enterprise_client');
        $this->addSql('DROP TABLE enterprise_client');
        $this->addSql('CREATE TABLE enterprise_client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, corporate_entity_id INTEGER NOT NULL, CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO enterprise_client (id, corporate_entity_id) SELECT id, corporate_entity_id FROM __temp__enterprise_client');
        $this->addSql('DROP TABLE __temp__enterprise_client');
        $this->addSql('CREATE INDEX IDX_54598E4C8BA692E5 ON enterprise_client (corporate_entity_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__individual_client AS SELECT id, person_id FROM individual_client');
        $this->addSql('DROP TABLE individual_client');
        $this->addSql('CREATE TABLE individual_client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, person_id INTEGER NOT NULL, CONSTRAINT FK_18764BB6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO individual_client (id, person_id) SELECT id, person_id FROM __temp__individual_client');
        $this->addSql('DROP TABLE __temp__individual_client');
        $this->addSql('CREATE INDEX IDX_18764BB6217BBB47 ON individual_client (person_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ite AS SELECT id, measurement_unit_id, source_id, city_id, project_type_id, quality, min, max, year_reference, comment, source_access FROM ite');
        $this->addSql('DROP TABLE ite');
        $this->addSql('CREATE TABLE ite (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, measurement_unit_id INTEGER NOT NULL, source_id INTEGER NOT NULL, city_id INTEGER NOT NULL, project_type_id INTEGER NOT NULL, quality VARCHAR(255) NOT NULL, min DOUBLE PRECISION NOT NULL, max DOUBLE PRECISION NOT NULL, year_reference INTEGER NOT NULL, comment CLOB DEFAULT NULL, source_access VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_CECC0098B6BD3460 FOREIGN KEY (measurement_unit_id) REFERENCES measurement_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CECC0098953C1C61 FOREIGN KEY (source_id) REFERENCES ite_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CECC00988BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CECC0098535280F6 FOREIGN KEY (project_type_id) REFERENCES ite_project_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ite (id, measurement_unit_id, source_id, city_id, project_type_id, quality, min, max, year_reference, comment, source_access) SELECT id, measurement_unit_id, source_id, city_id, project_type_id, quality, min, max, year_reference, comment, source_access FROM __temp__ite');
        $this->addSql('DROP TABLE __temp__ite');
        $this->addSql('CREATE INDEX IDX_CECC0098B6BD3460 ON ite (measurement_unit_id)');
        $this->addSql('CREATE INDEX IDX_CECC0098953C1C61 ON ite (source_id)');
        $this->addSql('CREATE INDEX IDX_CECC00988BAC62AF ON ite (city_id)');
        $this->addSql('CREATE INDEX IDX_CECC0098535280F6 ON ite (project_type_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__just_value_estimate AS SELECT id, building_id FROM just_value_estimate');
        $this->addSql('DROP TABLE just_value_estimate');
        $this->addSql('CREATE TABLE just_value_estimate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building_id INTEGER NOT NULL, CONSTRAINT FK_49630CA34D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_49630CA3BF396750 FOREIGN KEY (id) REFERENCES estimate (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO just_value_estimate (id, building_id) SELECT id, building_id FROM __temp__just_value_estimate');
        $this->addSql('DROP TABLE __temp__just_value_estimate');
        $this->addSql('CREATE INDEX IDX_49630CA34D2A7E12 ON just_value_estimate (building_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__project_technical_preparation_estimate AS SELECT id, building_id FROM project_technical_preparation_estimate');
        $this->addSql('DROP TABLE project_technical_preparation_estimate');
        $this->addSql('CREATE TABLE project_technical_preparation_estimate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building_id INTEGER NOT NULL, CONSTRAINT FK_39DA26B14D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_39DA26B1BF396750 FOREIGN KEY (id) REFERENCES estimate (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO project_technical_preparation_estimate (id, building_id) SELECT id, building_id FROM __temp__project_technical_preparation_estimate');
        $this->addSql('DROP TABLE __temp__project_technical_preparation_estimate');
        $this->addSql('CREATE INDEX IDX_39DA26B14D2A7E12 ON project_technical_preparation_estimate (building_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__representative AS SELECT id, phone, email FROM representative');
        $this->addSql('DROP TABLE representative');
        $this->addSql('CREATE TABLE representative (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, CONSTRAINT FK_2507390EBF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO representative (id, phone, email) SELECT id, phone, email FROM __temp__representative');
        $this->addSql('DROP TABLE __temp__representative');
        $this->addSql('CREATE TEMPORARY TABLE __temp__urbanization_estimate AS SELECT id, building_id FROM urbanization_estimate');
        $this->addSql('DROP TABLE urbanization_estimate');
        $this->addSql('CREATE TABLE urbanization_estimate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, building_id INTEGER NOT NULL, CONSTRAINT FK_89AA13F84D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_89AA13F8BF396750 FOREIGN KEY (id) REFERENCES estimate (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO urbanization_estimate (id, building_id) SELECT id, building_id FROM __temp__urbanization_estimate');
        $this->addSql('DROP TABLE __temp__urbanization_estimate');
        $this->addSql('CREATE INDEX IDX_89AA13F84D2A7E12 ON urbanization_estimate (building_id)');
    }
}
