<?php

namespace andytruong\dict\domain\idiom;

use Doctrine\DBAL\Connection;
use go1\edge\Edge;

class IdiomRepository
{
    private $connection;
    private $edge;

    public function __construct(Connection $connection, Edge $edge)
    {
        $this->connection = $connection;
        $this->edge = $edge;
    }

    public function getId($title)
    {
        return $this
            ->connection
            ->executeQuery("SELECT id FROM dict_idiom WHERE title = ?", [$title])
            ->fetchColumn();
    }
}
