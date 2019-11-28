<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126165743 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Migration for admin user and roles';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('INSERT INTO `role` VALUES (\'1\', \'user\', \'ROLE_USER\');
                            INSERT INTO `role` VALUES (\'2\', \'admin\', \'ROLE_ADMIN\');');
        $this->addSql('INSERT INTO `user` VALUES (\'1\', \'2\', \'admin@admin.adm\', \'$2y$13$aCruvq0srAxiBOnBauNHNOkDkr2.Nlg5iwk0yHm3Hvti.2nIH25yy\', \'2019-11-25 11:14:03\', \'2019-11-25 11:14:11\', \'NULL\');');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
