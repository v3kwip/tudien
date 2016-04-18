<?php

namespace andytruong\dict\domain\word;

use andytruong\dict\domain\Parser;
use andytruong\queue\Queue;
use Doctrine\DBAL\Connection;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WordWarmCommand extends Command
{
    private $connection;
    private $queue;
    private $parser;
    private $repository;

    public function __construct(Connection $connection, Queue $queue, Parser $parser, WordRepository $repository)
    {
        $this->connection = $connection;
        $this->queue = $queue;
        $this->parser = $parser;
        $this->repository = $repository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('word:warm')
            ->setDescription('Recrawl a word.')
            ->addArgument('title', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');

        if ($title) {
            $client = new Client();
            $url = 'http://www.oxfordlearnersdictionaries.com/definition/english/' . $title;
            $rules = Parser::fixRules(require __DIR__ . '/import/oxfordlearnersdictionaries.com.php');

            // Response for "love" unserialize.com/s/613d5830-bb73-a288-32eb-000044f04a89
            $response = $this->parser->parse($client->request('GET', $url), $rules);

            $this->repository->save($title, [
                'type'   => $response['type'],
                'idioms' => $response['idioms'],
                'data'   => [
                    'pronounces' => $response['pronounces'],
                    'means'      => $response['means'],
                    'related'    => $response['related'],
                ],
            ]);
        }

        $query = $this
            ->connection
            ->executeQuery(
                'SELECT w.title, s.url'
                . ' FROM dict_word w'
                . ' INNER JOIN dict_edge e ON w.id = e.source_id AND e.type = ?'
                . ' INNER JOIN dict_source s ON e.target_id = s.id',
                [Word::HAS_SOURCE]
            );

        while ($row = $query->fetch(\PDO::FETCH_OBJ)) {
            $this->queue->create('word.cmd.warm:warm', [$row->title, $row->url]);
        }
    }
}
