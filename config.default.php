<?php

use andytruong\dict\App;
use andytruong\dict\command\Worker;
use andytruong\dict\controller\StudyController;
use andytruong\dict\controller\topic\TopicDetailsController;
use andytruong\dict\controller\topic\TopicIndexController;
use andytruong\dict\controller\WordController;
use andytruong\dict\domain\Parser;
use andytruong\dict\domain\source\SourceRepository;
use andytruong\dict\domain\topic\TopicFetchCommand;
use andytruong\dict\domain\topic\TopicRepository;
use andytruong\dict\domain\user\User;
use andytruong\dict\domain\word\WordFetch;
use andytruong\dict\domain\word\WordRepository;
use andytruong\dict\domain\word\WordWarmCommand;
use andytruong\queue\Queue;
use go1\edge\Edge;
use go1\edge\EdgeEvent;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as Console;

return call_user_func(function () {
    date_default_timezone_set('UTC');

    if ($debug = getenv('APP_DEBUG') ?: true) {
        error_reporting(E_ALL);
        ini_set('display_errors', true);
    }

    $cnf = [
        'debug'           => $debug,
        'db.options'      => [
            'driver'        => 'pdo_mysql',
            'dbname'        => getenv('RDS_DB_NAME') ?: 'app_dict',
            'host'          => getenv('RDS_HOSTNAME') ?: '127.0.0.1',
            'user'          => getenv('RDS_USERNAME') ?: 'root',
            'password'      => getenv('RDS_PASSWORD') ?: '',
            'port'          => getenv('RDS_PORT') ?: '3306',
            'driverOptions' => [1002 => 'SET NAMES utf8'],
        ],
        'console'         => function (App $c) {
            $console = new Console(App::NAME, App::VERSION);
            $console->add($c['topic.cmd.fetch']);
            $console->add($c['worker']);
            $console->add($c['word.cmd.warm']);

            return $console;
        },
        'logger' => function () {
            $logger = new Logger(App::NAME);
            $logger->pushHandler(new ErrorLogHandler());

            return $logger;
        },
    ];

    return $cnf + [
        # Basic services
        # ---------------------
        'queue'  => function (App $c) { return new Queue($c['dbs']['default'], 'dict_queue', 'dict'); },
        'edge'   => function (App $c) { return new Edge($c['dbs']['default'], 'dict_edge', null, $c['dispatcher']); },
        'worker' => function (App $c) { return new Worker($c, $c['queue'], $c['callback_resolver'], $c['logger']); },

        # Domain
        # ---------------------
        'topic.repository'  => function (App $c) { return new TopicRepository($c['dbs']['default'], $c['edge']); },
        'topic.cmd.fetch'   => function (App $c) { return new TopicFetchCommand($c['queue']); },
        'source.repository' => function (App $c) { return new SourceRepository($c['dbs']['default'], $c['edge']); },
        'word.repository'   => function (App $c) { return new WordRepository($c['dbs']['default'], $c['edge'], $c['topic.repository'], $c['source.repository']); },
        'word.fetch'        => function (App $c) { return new WordFetch($c['topic.repository'], $c['word.repository'], $c['source.repository']); },
        'word.cmd.warm'     => function (App $c) { return new WordWarmCommand($c['dbs']['default'], $c['queue'], new Parser(), $c['word.repository']); },

        # Controller
        # ---------------------
        'ctrl.topic.index'   => function (App $c) { return new TopicIndexController($c['dbs']['default'], $c['topic.repository'], $c['edge']); },
        'ctrl.topic.details' => function (App $c) { return new TopicDetailsController($c['dbs']['default'], $c['topic.repository'], $c['edge']); },
        'ctrl.word'          => function (App $c) { return new WordController($c['dbs']['default'], $c['word.repository'], $c['topic.repository']); },
        'ctrl.study'         => function (App $c) { return new StudyController($c['edge'], $c['word.repository']); },

        # Events
        # ---------------------
        'events' => [
            Edge::EVENT_LINK_DUPLICATE => function (EdgeEvent $event) {
                $link = $event->getSubject();
                $weight = $link['weight'];

                switch ($link['type']) {
                    case User::VISIT_TOPIC:
                    case User::VISIT_WORD:
                        $event->setArgument('weight', 1 + $weight);
                        break;
                }
            },
        ],
    ];
});
