<?php

namespace andytruong\dict\controller;

use andytruong\dict\App;
use andytruong\dict\domain\topic\TopicRepository;
use andytruong\dict\domain\word\WordRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;

class WordController
{
    private $connection;
    private $wordRepository;
    private $topicRepository;

    public function __construct(
        Connection $connection,
        WordRepository $wordRepository,
        TopicRepository $topicRepository
    )
    {
        $this->connection = $connection;
        $this->wordRepository = $wordRepository;
        $this->topicRepository = $topicRepository;
    }

    private function load($id)
    {
        $word = $this->wordRepository->get($id);

        return new JsonResponse($word);
    }

    public function get($word)
    {
        if (!$id = $this->wordRepository->getId($word)) {
            return new JsonResponse([], 404);
        }

        return $this->load($id);
    }

    /**
     * Get a random word from an optional topic.
     *
     * @param string $topic
     * @return JsonResponse
     */
    public function getRandom($topic = null)
    {
        $q = $this->connection->createQueryBuilder();
        $q
            ->select('w.id')
            ->from('dict_word', 'w')
            ->orderBy('rand()')
            ->setMaxResults(1);

        if ($topic) {
            if (!$topicId = $this->topicRepository->getId($topic)) {
                return new JsonResponse(['message' => 'Topic not found.'], 404);
            }

            $q
                ->innerJoin('w', 'dict_edge', 'e', 'e.type = :has_topic AND w.id = e.source_id')
                ->setParameter(':has_topic', App::HAS_TOPIC)
                ->where('e.target_id IN (:topic_ids)')
                ->setParameter(
                    ':topic_ids',
                    $this->topicRepository->getLeafTopicId($topicId),
                    Connection::PARAM_INT_ARRAY
                );
        }

        if (!$id = $q->execute()->fetchColumn()) {
            return new JsonResponse([], 404);
        }

        return $this->load($id);
    }
}
