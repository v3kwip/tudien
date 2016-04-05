<?php

namespace andytruong\dict\controller;

use andytruong\dict\domain\word\WordRepository;
use go1\edge\Edge;

class StudyController
{
    private $edge;
    private $wordRepository;

    public function __construct(Edge $edge, WordRepository $wordRepository)
    {
        $this->edge = $edge;
        $this->wordRepository = $wordRepository;
    }

    public function createEdge($word, $type)
    {
        $userId = 1;
        $wordId = $this->wordRepository->getId($word);
        $this->edge->link($userId, $wordId, 0, $type);
    }
}
