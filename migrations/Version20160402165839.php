<?php

namespace andytruong\dict\migrations;

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

        $topic = $schema->createTable('dict_topic');
        $topic->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $topic->addColumn('title', 'string');
        $topic->addIndex(['title'], 'index_title');
        $topic->setPrimaryKey(['id']);

        $word = $schema->createTable('dict_source');
        $word->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $word->addColumn('title', 'string');
        $word->addColumn('description', 'text');
        $word->setPrimaryKey(['id']);

        $web = $schema->createTable('dict_web');
        $web->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $web->addColumn('url', 'string');
        $web->setPrimaryKey(['id']);
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
