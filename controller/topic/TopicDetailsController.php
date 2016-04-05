<?php

namespace andytruong\dict\controller\topic;

use andytruong\dict\App;
use andytruong\dict\domain\topic\TopicRepository;
use Doctrine\DBAL\Connection;
use go1\edge\Edge;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TopicDetailsController
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

    /**
     * Load topic details & list of words.
     *
     * @param int $topicId
     * @return Response
     */
    public function get($topicId)
    {
        $parentTopicId = $this->edge->getSourceIds($topicId, App::HAS_CHILD_TOPIC);
        if ($parentTopicId) {
            $parentTopicId = reset($parentTopicId);
            $rootId = $this->edge->getSourceIds($parentTopicId, App::HAS_CHILD_TOPIC);
            $rootId = $rootId ? $rootId[0] : null;
        }

        $wordIds = $this->edge->getSourceIds($topicId, App::HAS_TOPIC);
        if ($wordIds) {
            $words = $this
                ->connection
                ->executeQuery(
                    'SELECT * FROM dict_word WHERE id IN (?)',
                    [$wordIds],
                    [Connection::PARAM_INT_ARRAY]
                )
                ->fetchAll(PDO::FETCH_OBJ);

            return new JsonResponse(
                [
                    'rootId'   => $this->topicRepository->get($rootId),
                    'parentId' => $this->topicRepository->get($parentTopicId),
                    'words'    => $words,
                ]
            );
        }
    }
}
