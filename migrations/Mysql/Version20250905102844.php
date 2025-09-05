<?php

declare(strict_types=1);

namespace DoctrineMigrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250905102844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, land_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, stop_reason LONGTEXT DEFAULT NULL, estimated_value_construction BIGINT NOT NULL, estimated_value_equipment BIGINT NOT NULL, estimated_value_other BIGINT NOT NULL, approved_value_construction BIGINT NOT NULL, approved_value_equipment BIGINT NOT NULL, approved_value_other BIGINT NOT NULL, is_new TINYINT(1) DEFAULT NULL, population INT NOT NULL, construction_assembly BIGINT NOT NULL, construction_assembly_comment LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, has_reply TINYINT(1) DEFAULT NULL, INDEX IDX_E16F61D4166D1F9C (project_id), UNIQUE INDEX UNIQ_E16F61D41994904A (land_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, representative_id INT DEFAULT NULL, municipality_id INT NOT NULL, address LONGTEXT NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_C7440455FC3FF006 (representative_id), INDEX IDX_C7440455AE6F181C (municipality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE constructive_action (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE constructor (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX constructor_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE constructor_building (id INT AUTO_INCREMENT NOT NULL, constructor_id INT NOT NULL, building_id INT NOT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_29A127572D98BF9 (constructor_id), INDEX IDX_29A127574D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, year INT NOT NULL, UNIQUE INDEX contract_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE corporate_entity (id INT AUTO_INCREMENT NOT NULL, organism_id INT NOT NULL, municipality_id INT NOT NULL, code VARCHAR(255) NOT NULL, nit VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C6EFC8A464180A36 (organism_id), INDEX IDX_C6EFC8A4AE6F181C (municipality_id), UNIQUE INDEX corporate_entity_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draftsman (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE draftsman_building (id INT AUTO_INCREMENT NOT NULL, draftsman_id INT NOT NULL, building_id INT NOT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F9CF0F7B40C866FD (draftsman_id), INDEX IDX_F9CF0F7B4D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enterprise_client (id INT NOT NULL, corporate_entity_id INT NOT NULL, INDEX IDX_54598E4C8BA692E5 (corporate_entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, original_id INT DEFAULT NULL, ground_floor TINYINT(1) NOT NULL, position INT NOT NULL, name VARCHAR(255) NOT NULL, has_reply TINYINT(1) DEFAULT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_BE45D62E4D2A7E12 (building_id), UNIQUE INDEX UNIQ_BE45D62E108B7592 (original_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE geographic_location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE individual_client (id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_18764BB6217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE investment (id INT AUTO_INCREMENT NOT NULL, location_zone_id INT DEFAULT NULL, municipality_id INT NOT NULL, between_streets LONGTEXT DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, popular_council VARCHAR(255) DEFAULT NULL, block VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, street VARCHAR(255) NOT NULL, address_number VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_43CA0AD671692C0F (location_zone_id), INDEX IDX_43CA0AD6AE6F181C (municipality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE land (id INT AUTO_INCREMENT NOT NULL, land_area INT NOT NULL, occupied_area INT NOT NULL, perimeter INT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, microlocalization VARCHAR(255) DEFAULT NULL, floor INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE land_network_connection (id INT AUTO_INCREMENT NOT NULL, land_id INT NOT NULL, network_connection_id INT NOT NULL, explanation LONGTEXT DEFAULT NULL, INDEX IDX_7BF47B3B1994904A (land_id), INDEX IDX_7BF47B3B4EDCA4BC (network_connection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE local (id INT AUTO_INCREMENT NOT NULL, sub_system_id INT NOT NULL, local_constructive_action_id INT DEFAULT NULL, original_id INT DEFAULT NULL, number INT NOT NULL, area INT NOT NULL, type VARCHAR(255) NOT NULL, height DOUBLE PRECISION NOT NULL, technical_status VARCHAR(255) NOT NULL, impact_higher_levels TINYINT(1) NOT NULL, comment LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, has_reply TINYINT(1) DEFAULT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_8BD688E8C298691B (sub_system_id), UNIQUE INDEX UNIQ_8BD688E8460C8FAC (local_constructive_action_id), UNIQUE INDEX UNIQ_8BD688E8108B7592 (original_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE local_constructive_action (id INT AUTO_INCREMENT NOT NULL, constructive_action_id INT DEFAULT NULL, price BIGINT NOT NULL, INDEX IDX_EECA25F5369941F1 (constructive_action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location_zone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipality (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C6F56628E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE network_connection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organism (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX organism_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(255) NOT NULL, identification_number VARCHAR(255) NOT NULL, passport VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, discr VARCHAR(255) DEFAULT \'other\' NOT NULL, UNIQUE INDEX person_identification_number (identification_number), UNIQUE INDEX person_passport (passport), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, contract_id INT DEFAULT NULL, investment_id INT NOT NULL, currency_id INT NOT NULL, type VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, stop_reason LONGTEXT DEFAULT NULL, register_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', stopped_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', canceled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', initiated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', terrain_diagnosis_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', urban_regulation_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', design_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', comment LONGTEXT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2FB3D0EE19EB6921 (client_id), UNIQUE INDEX UNIQ_2FB3D0EE2576E0FD (contract_id), INDEX IDX_2FB3D0EE6E1B4FD5 (investment_id), INDEX IDX_2FB3D0EE38248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX province_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE representative (id INT NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, importance INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX role_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_system (id INT AUTO_INCREMENT NOT NULL, floor_id INT NOT NULL, original_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, has_reply TINYINT(1) DEFAULT NULL, state VARCHAR(255) NOT NULL, INDEX IDX_7B1C5EC7854679E2 (floor_id), UNIQUE INDEX UNIQ_7B1C5EC7108B7592 (original_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649217BBB47 (person_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D41994904A FOREIGN KEY (land_id) REFERENCES land (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455FC3FF006 FOREIGN KEY (representative_id) REFERENCES representative (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE constructor_building ADD CONSTRAINT FK_29A127572D98BF9 FOREIGN KEY (constructor_id) REFERENCES constructor (id)');
        $this->addSql('ALTER TABLE constructor_building ADD CONSTRAINT FK_29A127574D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE corporate_entity ADD CONSTRAINT FK_C6EFC8A464180A36 FOREIGN KEY (organism_id) REFERENCES organism (id)');
        $this->addSql('ALTER TABLE corporate_entity ADD CONSTRAINT FK_C6EFC8A4AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE draftsman ADD CONSTRAINT FK_19A4FE4ABF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE draftsman_building ADD CONSTRAINT FK_F9CF0F7B40C866FD FOREIGN KEY (draftsman_id) REFERENCES draftsman (id)');
        $this->addSql('ALTER TABLE draftsman_building ADD CONSTRAINT FK_F9CF0F7B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE enterprise_client ADD CONSTRAINT FK_54598E4C8BA692E5 FOREIGN KEY (corporate_entity_id) REFERENCES corporate_entity (id)');
        $this->addSql('ALTER TABLE enterprise_client ADD CONSTRAINT FK_54598E4CBF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E108B7592 FOREIGN KEY (original_id) REFERENCES floor (id)');
        $this->addSql('ALTER TABLE individual_client ADD CONSTRAINT FK_18764BB6217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE individual_client ADD CONSTRAINT FK_18764BB6BF396750 FOREIGN KEY (id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE investment ADD CONSTRAINT FK_43CA0AD671692C0F FOREIGN KEY (location_zone_id) REFERENCES location_zone (id)');
        $this->addSql('ALTER TABLE investment ADD CONSTRAINT FK_43CA0AD6AE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('ALTER TABLE land_network_connection ADD CONSTRAINT FK_7BF47B3B1994904A FOREIGN KEY (land_id) REFERENCES land (id)');
        $this->addSql('ALTER TABLE land_network_connection ADD CONSTRAINT FK_7BF47B3B4EDCA4BC FOREIGN KEY (network_connection_id) REFERENCES network_connection (id)');
        $this->addSql('ALTER TABLE local ADD CONSTRAINT FK_8BD688E8C298691B FOREIGN KEY (sub_system_id) REFERENCES sub_system (id)');
        $this->addSql('ALTER TABLE local ADD CONSTRAINT FK_8BD688E8460C8FAC FOREIGN KEY (local_constructive_action_id) REFERENCES local_constructive_action (id)');
        $this->addSql('ALTER TABLE local ADD CONSTRAINT FK_8BD688E8108B7592 FOREIGN KEY (original_id) REFERENCES local (id)');
        $this->addSql('ALTER TABLE local_constructive_action ADD CONSTRAINT FK_EECA25F5369941F1 FOREIGN KEY (constructive_action_id) REFERENCES constructive_action (id)');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE2576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE6E1B4FD5 FOREIGN KEY (investment_id) REFERENCES investment (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE representative ADD CONSTRAINT FK_2507390EBF396750 FOREIGN KEY (id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_system ADD CONSTRAINT FK_7B1C5EC7854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('ALTER TABLE sub_system ADD CONSTRAINT FK_7B1C5EC7108B7592 FOREIGN KEY (original_id) REFERENCES sub_system (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4166D1F9C');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D41994904A');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455FC3FF006');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455AE6F181C');
        $this->addSql('ALTER TABLE constructor_building DROP FOREIGN KEY FK_29A127572D98BF9');
        $this->addSql('ALTER TABLE constructor_building DROP FOREIGN KEY FK_29A127574D2A7E12');
        $this->addSql('ALTER TABLE corporate_entity DROP FOREIGN KEY FK_C6EFC8A464180A36');
        $this->addSql('ALTER TABLE corporate_entity DROP FOREIGN KEY FK_C6EFC8A4AE6F181C');
        $this->addSql('ALTER TABLE draftsman DROP FOREIGN KEY FK_19A4FE4ABF396750');
        $this->addSql('ALTER TABLE draftsman_building DROP FOREIGN KEY FK_F9CF0F7B40C866FD');
        $this->addSql('ALTER TABLE draftsman_building DROP FOREIGN KEY FK_F9CF0F7B4D2A7E12');
        $this->addSql('ALTER TABLE enterprise_client DROP FOREIGN KEY FK_54598E4C8BA692E5');
        $this->addSql('ALTER TABLE enterprise_client DROP FOREIGN KEY FK_54598E4CBF396750');
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E4D2A7E12');
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E108B7592');
        $this->addSql('ALTER TABLE individual_client DROP FOREIGN KEY FK_18764BB6217BBB47');
        $this->addSql('ALTER TABLE individual_client DROP FOREIGN KEY FK_18764BB6BF396750');
        $this->addSql('ALTER TABLE investment DROP FOREIGN KEY FK_43CA0AD671692C0F');
        $this->addSql('ALTER TABLE investment DROP FOREIGN KEY FK_43CA0AD6AE6F181C');
        $this->addSql('ALTER TABLE land_network_connection DROP FOREIGN KEY FK_7BF47B3B1994904A');
        $this->addSql('ALTER TABLE land_network_connection DROP FOREIGN KEY FK_7BF47B3B4EDCA4BC');
        $this->addSql('ALTER TABLE local DROP FOREIGN KEY FK_8BD688E8C298691B');
        $this->addSql('ALTER TABLE local DROP FOREIGN KEY FK_8BD688E8460C8FAC');
        $this->addSql('ALTER TABLE local DROP FOREIGN KEY FK_8BD688E8108B7592');
        $this->addSql('ALTER TABLE local_constructive_action DROP FOREIGN KEY FK_EECA25F5369941F1');
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F56628E946114A');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE19EB6921');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE2576E0FD');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE6E1B4FD5');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE38248176');
        $this->addSql('ALTER TABLE representative DROP FOREIGN KEY FK_2507390EBF396750');
        $this->addSql('ALTER TABLE sub_system DROP FOREIGN KEY FK_7B1C5EC7854679E2');
        $this->addSql('ALTER TABLE sub_system DROP FOREIGN KEY FK_7B1C5EC7108B7592');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649217BBB47');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
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
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_role');
    }
}
