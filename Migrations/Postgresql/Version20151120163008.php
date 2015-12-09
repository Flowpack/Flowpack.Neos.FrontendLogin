<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Add "user" domain model (initial migration)
 */
class Version20151120163008 extends AbstractMigration
{

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

        $this->addSql("CREATE TABLE flowpack_neos_frontendlogin_domain_model_user (persistence_object_identifier VARCHAR(40) NOT NULL, givenname VARCHAR(255) NOT NULL, familyname VARCHAR(255) NOT NULL, emailaddress VARCHAR(255) DEFAULT NULL, PRIMARY KEY(persistence_object_identifier))");
        $this->addSql("CREATE TABLE flowpack_neos_frontendlogin_domain_model_user_accounts_join (frontendlogin_user VARCHAR(40) NOT NULL, flow_security_account VARCHAR(40) NOT NULL, PRIMARY KEY(frontendlogin_user, flow_security_account))");
        $this->addSql("CREATE INDEX IDX_4B3E2FA24FD868C2 ON flowpack_neos_frontendlogin_domain_model_user_accounts_join (frontendlogin_user)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_4B3E2FA258842EFC ON flowpack_neos_frontendlogin_domain_model_user_accounts_join (flow_security_account)");
        $this->addSql("ALTER TABLE flowpack_neos_frontendlogin_domain_model_user_accounts_join ADD CONSTRAINT FK_4B3E2FA24FD868C2 FOREIGN KEY (frontendlogin_user) REFERENCES flowpack_neos_frontendlogin_domain_model_user (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE flowpack_neos_frontendlogin_domain_model_user_accounts_join ADD CONSTRAINT FK_4B3E2FA258842EFC FOREIGN KEY (flow_security_account) REFERENCES typo3_flow_security_account (persistence_object_identifier) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");

        $this->addSql("ALTER TABLE flowpack_neos_frontendlogin_domain_model_user_accounts_join DROP CONSTRAINT FK_4B3E2FA24FD868C2");
        $this->addSql("DROP TABLE flowpack_neos_frontendlogin_domain_model_user");
        $this->addSql("DROP TABLE flowpack_neos_frontendlogin_domain_model_user_accounts_join");
    }
}