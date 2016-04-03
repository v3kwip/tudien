<?php

namespace andytruong\dict\commands;

use andytruong\dict\App;
use andytruong\queue\Exception;
use andytruong\queue\Queue;
use Psr\Log\LoggerInterface;
use Silex\CallbackResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Worker extends Command
{
    private $app;
    private $queue;
    private $resolver;
    private $logger;

    public function __construct(
        App $app,
        Queue $queue,
        CallbackResolver $resolver,
        LoggerInterface $logger)
    {
        $this->app = $app;
        $this->queue = $queue;
        $this->resolver = $resolver;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('worker')
            ->addArgument('queue', InputArgument::OPTIONAL, 'Queue name, by default consume will fetch all queue.', false)
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Limit messages to be processed.', -1)
            ->addOption('timeout', null, InputOption::VALUE_OPTIONAL, 'Timeout limit for each message.', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue');
        $limit = $input->getOption('limit');
        $timeout = $input->getOption('timeout');

        $this->queue->consume(
            function ($message) {
                $this->logger->info("Processing %name #%id", ['%name' => $message->name, '%id' => $message->id]);
                list($handler, $arguments) = $message->body;

                call_user_func_array(
                    is_callable($handler) ? $handler : $this->resolver->resolveCallback($handler),
                    $arguments
                );
            },
            $errorHandler = function(Exception $e) {
                throw $e;
            },
            $limit,
            $timeout,
            $queue
        );
    }
}
