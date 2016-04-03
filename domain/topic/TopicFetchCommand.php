<?php

namespace andytruong\dict\domain\topic;

use andytruong\queue\Queue;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class TopicFetchCommand extends Command
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('fetch:topic');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tree = [];

        $client = new Client();
        $crawler = $client->request('GET', 'http://www.oxfordlearnersdictionaries.com/topic/');

        $crawler
            ->filter('#topic-list > dd > dl > dd > ul > li > a')
            ->each(
                function (Crawler $node) use (&$tree) {
                    $category = $node->parents()->first()->parents()->first()->parents()->first()->parents()->first()->children()->first();
                    $root = $category->parents()->first()->parents()->first()->previousAll()->first();
                    $url = $node->attr('href');

                    $tree[trim($root->text())][trim($category->text())][trim($node->text())] = $url;
                }
            );

        foreach ($tree as $root => $categories) {
            foreach ($categories as $category => $topics) {
                foreach ($topics as $topic => $url) {
                    $this->queue->create(
                        'topic.cmd.fetch:fetch',
                        [$root, $category, $topic, $url],
                        Queue::PRIORITY_NORMAL
                    );
                }
            }
        }
    }

    public function fetch($root, $category, $topic, $url)
    {
        $client = new Client();
        $client
            ->request('GET', $url)
            ->filter('#main-container .tint_panel ul.wordpool > li > a')
            ->each(function (Crawler $node) use ($root, $category, $topic) {
                $word = trim($node->text());
                $link = $node->attr('href');

                $this->queue->create(
                    'word.fetch:fetch',
                    [$root, $category, $topic, $word, $link]
                );
            });
    }
}
