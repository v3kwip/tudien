<?php

namespace andytruong\dict\domain\topic;

use Doctrine\DBAL\Schema\Schema;

class Topic
{
    const HAS_CHILD = 200;

    public static function install(Schema $schema)
    {
        $table = $schema->createTable('dict_topic');
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('title', 'string');
        $table->addIndex(['title'], 'index_title');
        $table->setPrimaryKey(['id']);
    }
}
