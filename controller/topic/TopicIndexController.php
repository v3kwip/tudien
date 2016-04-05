<?php

namespace andytruong\dict\controller\topic;

use andytruong\dict\App;
use andytruong\dict\domain\topic\TopicRepository;
use Doctrine\DBAL\Connection;
use go1\edge\Edge;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TopicIndexController
{
    private $connection;
    private $topicRepository;
    private $edge;

    public function __construct(
        Connection $connection,
        TopicRepository $topicRepository,
        Edge $edge)
    {
        $this->connection = $connection;
        $this->topicRepository = $topicRepository;
        $this->edge = $edge;
    }

    public function get($parentTopicId = null, Request $request)
    {
        $q = $this->connection->createQueryBuilder();
        $q
            ->select('t.id')
            ->from('dict_topic', 't')
            ->orderBy('t.title')
            ->setFirstResult((int) $request->get('offset', 0))
            ->setMaxResults((int) $request->get('limit', 100));

        // Join the edge to find parent.
        $q
            ->leftJoin('t', 'dict_edge', 'edge', 't.id = edge.target_id AND edge.type = :edge_type')
            ->setParameter(':edge_type', App::HAS_CHILD_TOPIC);

        // Input parent topic ID is null => require root.
        if (null === $parentTopicId) {
            $q->where($q->expr()->isNull('edge.source_id'));
        }
        else {
            $q
                ->where('edge.source_id = :parentId')
                ->setParameter(':parentId', $parentTopicId);
        }

        $ids = $topicIds = $q->execute()->fetchAll(PDO::FETCH_COLUMN);
        $subTopicIds = $this->edge->getTargetIds($topicIds, App::HAS_CHILD_TOPIC);
        if ($subTopicIds) {
            $_subIds = [];
            foreach ($subTopicIds as $subIds) {
                $ids = array_merge($ids, $subIds);
                $_subIds = array_merge($_subIds, $subIds);
            }

            $leafTopicIds = $this->edge->getTargetIds($_subIds, App::HAS_CHILD_TOPIC);
            if ($leafTopicIds) {
                foreach ($leafTopicIds as $leafIds) {
                    $ids = array_merge($ids, $leafIds);
                }
            }
        }

        return $this->topics($ids, $topicIds, $subTopicIds, !empty($leafTopicIds) ? $leafTopicIds : []);
    }

    private function topics($ids, $topicIds, $subTopicIds, $leafTopicIds)
    {
        $topicQuery = $this
            ->connection
            ->executeQuery(
                'SELECT * FROM dict_topic WHERE id IN (?)',
                [$ids],
                [Connection::PARAM_INT_ARRAY]
            );

        $tree = [];
        while ($node = $topicQuery->fetch()) {
            $this->addNode($tree, $topicIds, $subTopicIds, $leafTopicIds, $node);
        }

        return new JsonResponse($tree);
    }

    private function addNode(array &$tree, array &$topicIds, array &$subTopicIds, array &$leafTopicIds, array &$node)
    {
        $included = false;
        foreach ($topicIds as &$topicId) {
            if ($topicId == $node['id']) {
                $included = true;
                $tree[$topicId] = isset($tree[$topicId]) ? array_merge($node, $tree[$topicId]) : $node;
                continue;
            }
        }

        if (!$included) {
            foreach ($subTopicIds as $rootId => &$subIds) {
                foreach ($subIds as &$subId) {
                    if ($subId == $node['id']) {
                        $included = true;
                        $tree[$rootId]['items'][$subId] = isset($tree[$rootId]['items'][$subId]) ? array_merge($node, $tree[$rootId]['items'][$subId]) : $node;
                        break;
                    }
                }
            }
        }

        if (!$included && !empty($leafTopicIds)) {
            foreach ($leafTopicIds as $subTopicId => $leafIds) {
                foreach ($leafIds as $leafId) {
                    if ($leafId == $node['id']) {
                        foreach ($subTopicIds as $rootId => $subIds) {
                            foreach ($subIds as $subId) {
                                if ($subId == $subTopicId) {
                                    $tree[$rootId]['items'][$subId]['items'][$leafId] = $node;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
