<?php

namespace andytruong\dict\domain\word;

use andytruong\dict\App;
use andytruong\dict\domain\idiom\IdiomRepository;
use andytruong\dict\domain\source\SourceRepository;
use andytruong\dict\domain\topic\TopicRepository;
use Doctrine\DBAL\Connection;
use go1\edge\Edge;
use PDO;

class WordRepository
{
    private $connection;
    private $edge;
    private $topicRepository;
    private $sourceRepository;

    public function __construct(
        Connection $connection,
        Edge $edge,
        TopicRepository $topicRepository,
        SourceRepository $sourceRepository)
    {
        $this->connection = $connection;
        $this->edge = $edge;
        $this->topicRepository = $topicRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function getId($title)
    {
        return $this
            ->connection
            ->executeQuery("SELECT id FROM dict_word WHERE title = ?", [$title])
            ->fetchColumn();
    }

    public function get($id)
    {
        $word = $this
            ->connection
            ->executeQuery('SELECT * FROM dict_word WHERE id = ?', [$id])
            ->fetch(PDO::FETCH_OBJ);

        return $word;
    }

    public function save($title, array $options, IdiomRepository $idioRepository)
    {
        $id = $this->getId($title) ?: $this->create($title);
        $data = [];

        foreach ($options as $key => $value) {
            switch ($key) {
                case 'type':
                case 'data':
                    $data[$key] = $value;
                    break;

                case 'idioms':
                    $data['data']['idiom'] = $value;
                    break;
            }
        }

        return $this->connection->update('dict_word', $data, ['id' => $id]) ? true : false;
    }

    public function create($title)
    {
        $this
            ->connection
            ->insert('dict_word', ['title' => $title]);

        return $this->connection->lastInsertId('dict_word');
    }

    public function linkTopic($word, $topic)
    {
        $sourceId = $this->getId($word);
        $targetId = $this->topicRepository->getId($topic);

        return $this->edge->link($sourceId, $targetId, 0, App::HAS_TOPIC);
    }

    public function linkSource($word, $url)
    {
        $sourceId = $this->getId($word);
        $targetId = $this->sourceRepository->getId($url);

        return $this->edge->link($sourceId, $targetId, 0, App::HAS_SOURCE);
    }
}
