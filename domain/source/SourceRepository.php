<?php

namespace andytruong\dict\domain\source;

use Doctrine\DBAL\Connection;
use go1\edge\Edge;

class SourceRepository
{
    private $connection;
    private $edge;

    public function __construct(Connection $connection, Edge $edge)
    {
        $this->connection = $connection;
        $this->edge = $edge;
    }

    public function getId($url)
    {
        return $this
            ->connection
            ->executeQuery("SELECT id FROM dict_source WHERE url = ?", [$url])
            ->fetchColumn();
    }

    public function create($url)
    {
        $this
            ->connection
            ->insert('dict_source', ['url' => $url]);

        return $this->connection->lastInsertId('dict_source');
    }
}
