<?php

namespace andytruong\dict\domain\word;

use andytruong\dict\App;
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

    public function __construct(Connection $connection, Queue $queue)
    {
        $this->connection = $connection;
        $this->queue = $queue;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('word:warm')
            ->addArgument('title', InputArgument::OPTIONAL)
            ->addArgument('url', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');
        $url = $input->getArgument('url');

        if ($title && $url) {
            return $this->warm($title, $url);
        }

        $query = $this
            ->connection
            ->executeQuery(
                'SELECT w.title, s.url'
                . ' FROM dict_word w'
                . ' INNER JOIN dict_edge e ON w.id = e.source_id AND e.type = ?'
                . ' INNER JOIN dict_source s ON e.target_id = s.id',
                [App::HAS_SOURCE]
            );

        while ($row = $query->fetch(\PDO::FETCH_OBJ)) {
            $this->queue->create('word.cmd.warm:warm', [$row->title, $row->url]);
        }
    }

    public function warm($title, $url)
    {
        $url = 'http://www.oxfordlearnersdictionaries.com/definition/english/' . $title;
        $client = new Client();
        $content = $client->request('GET', $url)->filter('#entryContent')->first();

        print_r([
            'top'   => $content->filter('.top-container')->html(),
            'entry' => $content->filter('span.def')
                               ->parents()->first()
                               ->parents()->first()
                               ->html(),
            // 'full' => $content->html(),
        ]);
    }
}
