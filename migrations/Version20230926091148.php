<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926091148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program_session DROP FOREIGN KEY FK_5BAE2D003EB8070A');
        $this->addSql('ALTER TABLE program_session DROP FOREIGN KEY FK_5BAE2D00613FECDF');
        $this->addSql('ALTER TABLE program_module DROP FOREIGN KEY FK_586418723EB8070A');
        $this->addSql('ALTER TABLE program_module DROP FOREIGN KEY FK_58641872AFC2B591');
        $this->addSql('DROP TABLE program_session');
        $this->addSql('DROP TABLE program_module');
        $this->addSql('ALTER TABLE program ADD session_id INT NOT NULL, ADD module_id INT NOT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('CREATE INDEX IDX_92ED7784613FECDF ON program (session_id)');
        $this->addSql('CREATE INDEX IDX_92ED7784AFC2B591 ON program (module_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE program_session (program_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_5BAE2D00613FECDF (session_id), INDEX IDX_5BAE2D003EB8070A (program_id), PRIMARY KEY(program_id, session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE program_module (program_id INT NOT NULL, module_id INT NOT NULL, INDEX IDX_58641872AFC2B591 (module_id), INDEX IDX_586418723EB8070A (program_id), PRIMARY KEY(program_id, module_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE program_session ADD CONSTRAINT FK_5BAE2D003EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_session ADD CONSTRAINT FK_5BAE2D00613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_module ADD CONSTRAINT FK_586418723EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_module ADD CONSTRAINT FK_58641872AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784613FECDF');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784AFC2B591');
        $this->addSql('DROP INDEX IDX_92ED7784613FECDF ON program');
        $this->addSql('DROP INDEX IDX_92ED7784AFC2B591 ON program');
        $this->addSql('ALTER TABLE program DROP session_id, DROP module_id');
    }
}
