<?php

namespace andytruong\dict\domain\word;

use Doctrine\DBAL\Schema\Schema;

class Word
{
    const HAS_TOPIC         = 200;
    const HAS_SOURCE        = 201;
    const HAS_PRONOUNCE     = 202;
    const HAS_MEAN          = 203;
    const HAS_IDIOM         = 204;
    const HAS_RELATED_WORD  = 205;
    const HAS_RELATED_IDIOM = 206;

    public static function install(Schema $schema)
    {
        $table = $schema->createTable('dict_word');
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('type', 'text');
        $table->addColumn('title', 'string');
        $table->addColumn('data', 'blob');
        $table->setPrimaryKey(['id']);
    }
}
