<?php

declare(strict_types=1);

namespace DoctrineMigrations\Sqlite;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250918152140 extends AbstractMigration
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__representative AS SELECT id, phone, email FROM representative');
        $this->addSql('DROP TABLE representative');
        $this->addSql('CREATE TABLE representative (id INTEGER NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2507390EBF396750 FOREIGN KEY (id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO representative (id, phone, email) SELECT id, phone, email FROM __temp__representative');
        $this->addSql('DROP TABLE __temp__representative');
        $this->addSql('CREATE TEMPORARY TABLE __temp__urban_regulation AS SELECT id, type_id, code, description, data, measurement_unit, photo, comment, legal_reference FROM urban_regulation');
        $this->addSql('DROP TABLE urban_regulation');
        $this->addSql('CREATE TABLE urban_regulation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER NOT NULL, code VARCHAR(255) NOT NULL, description CLOB NOT NULL, data VARCHAR(255) NOT NULL, measurement_unit VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, comment CLOB DEFAULT NULL, legal_reference VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_C3CB3A23C54C8C93 FOREIGN KEY (type_id) REFERENCES urban_regulation_type (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO urban_regulation (id, type_id, code, description, data, measurement_unit, photo, comment, legal_reference) SELECT id, type_id, code, description, data, measurement_unit, photo, comment, legal_reference FROM __temp__urban_regulation');
        $this->addSql('DROP TABLE __temp__urban_regulation');
        $this->addSql('CREATE INDEX IDX_C3CB3A23C54C8C93 ON urban_regulation (type_id)');
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__representative AS SELECT id, phone, email FROM representative');
        $this->addSql('DROP TABLE representative');
        $this->addSql('CREATE TABLE representative (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, CONSTRAINT FK_2507390EBF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO representative (id, phone, email) SELECT id, phone, email FROM __temp__representative');
        $this->addSql('DROP TABLE __temp__representative');
        $this->addSql('CREATE TEMPORARY TABLE __temp__urban_regulation AS SELECT id, type_id, code, description, data, measurement_unit, photo, comment, legal_reference FROM urban_regulation');
        $this->addSql('DROP TABLE urban_regulation');
        $this->addSql('CREATE TABLE urban_regulation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER NOT NULL, code VARCHAR(255) NOT NULL, description CLOB NOT NULL, data VARCHAR(255) NOT NULL, measurement_unit VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, comment CLOB DEFAULT NULL, legal_reference VARCHAR(255) NOT NULL, CONSTRAINT FK_C3CB3A23C54C8C93 FOREIGN KEY (type_id) REFERENCES urban_regulation_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO urban_regulation (id, type_id, code, description, data, measurement_unit, photo, comment, legal_reference) SELECT id, type_id, code, description, data, measurement_unit, photo, comment, legal_reference FROM __temp__urban_regulation');
        $this->addSql('DROP TABLE __temp__urban_regulation');
        $this->addSql('CREATE INDEX IDX_C3CB3A23C54C8C93 ON urban_regulation (type_id)');
    }
}
