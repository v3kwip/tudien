<?php

namespace andytruong\dict\domain\topic;

use andytruong\dict\App;
use Doctrine\DBAL\Connection;
use go1\edge\Edge;

class TopicRepository
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
            ->executeQuery("SELECT id FROM dict_topic WHERE title = ?", [$title])
            ->fetchColumn();
    }

    public function get($id)
    {
        return $this
            ->connection
            ->executeQuery("SELECT * FROM dict_topic WHERE id = ?", [$id])
            ->fetch(\PDO::FETCH_OBJ);
    }

    public function create($title)
    {
        $this
            ->connection
            ->insert('dict_topic', ['title' => $title]);

        return $this->connection->lastInsertId('dict_topic');
    }

    public function linkSubTopic($parent, $children)
    {
        $sourceId = $this->getId($parent);
        $targetId = $this->getId($children);

        return $this->edge->link($sourceId, $targetId, 0, App::HAS_CHILD_TOPIC);
    }

    /**
     * @param int|int[] $ids
     * @return int[]
     */
    public function getLeafTopicId($ids)
    {
        $ids = is_scalar($ids) ? [$ids] : $ids;
        $subTopicIDs = $this->edge->getTargetIds($ids, App::HAS_CHILD_TOPIC);
        if ($subTopicIDs) {
            $subIds = [];
            foreach ($subTopicIDs as $subTopicID) {
                $subIds = array_merge($subIds, $subTopicID);
            }

            return $this->getLeafTopicId($subIds);
        }

        return $ids;
    }
}
