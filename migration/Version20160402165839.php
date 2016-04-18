<?php

namespace andytruong\dict\migration;

use andytruong\dict\domain\topic\Topic;
use andytruong\dict\domain\word\Word;
use andytruong\queue\Queue;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use go1\edge\Edge;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160402165839 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        Edge::migrate($schema, 'dict_edge');
        Queue::migrate($schema, 'dict_queue');

        Topic::install($schema);
        Word::install($schema);

        $web = $schema->createTable('dict_source');
        $web->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $web->addColumn('url', 'string');
        $web->setPrimaryKey(['id']);

        $idiom = $schema->createTable('dict_idiom');
        $idiom->addColumn('id', 'integer', ['unsign' => true, 'autoincrement' => true]);
        $idiom->addColumn('description', 'text');
        $idiom->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('dict_topic');
        $schema->dropTable('dict_source');
        $schema->dropTable('dict_web');
        $schema->dropTable('dict_ro');
        $schema->dropTable('dict_queue');
    }
}
