<?php

namespace andytruong\dict;

use andytruong\dict\domain\user\User;
use go1\app\App as GO1;

class App extends GO1
{
    const NAME    = 'andytruong/dictionary';
    const VERSION = 'v1.0';

    public function __construct(array $values)
    {
        parent::__construct($values);

        // Topic
        // ---------------------
        $this->get('/topic/index/{parentTopicId}', 'ctrl.topic.index:get')->value('parentTopicId', null);
        $this->get('/topic/{topicId}', 'ctrl.topic.details:get')->assert('topicId', '\d+');
        $this->post('/topic/edge/{topic}/{type}', 'ctrl.study:index')->value('topic', User::VISIT_TOPIC);
        $this->post('/word/edge/{word}/{type}', 'ctrl.study:createEdge')->value('type', User::KNOW_WORD);

        // Word
        // ---------------------
        $this->get('/word/{word}', 'ctrl.word:get'); # @TODO: Load topics
        $this->get('/word/random/{topic}', 'ctrl.word:getRandom')->value('topic', null);
    }
}
