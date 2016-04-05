<?php

namespace andytruong\dict\controller;

use andytruong\dict\domain\word\WordRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;

class WordController
{
    private $connection;
    private $wordRepository;

    public function __construct(Connection $connection, WordRepository $wordRepository)
    {
        $this->connection = $connection;
        $this->wordRepository = $wordRepository;
    }

    public function get($word)
    {
        if (!$id = $this->wordRepository->getId($word)) {
            return new JsonResponse([], 404);
        }

        $word = $this->wordRepository->get($id);

        return new JsonResponse($word);
    }

    public function getRandom($topic = null)
    {
    }
}
