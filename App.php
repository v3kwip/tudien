<?php

namespace andytruong\dict;

use go1\app\App as GO1;

class App extends GO1
{
    const NAME    = 'andytruong/dictionary';
    const VERSION = 'v1.0';

    const HAS_CHILD_TOPIC = 200;
    const HAS_TOPIC       = 201;
    const HAS_SOURCE      = 202;
    const EDGE_KNOW_WORD  = 203;

    public function __construct(array $values)
    {
        parent::__construct($values);

        $this->get('/topic/index/{parentTopicId}', 'ctrl.topic.index:get')->value('parentTopicId', null);
        $this->get('/topic/{topicId}', 'ctrl.topic.details:get');
        $this->get('/word/{word}', 'ctrl.word:get'); # @TODO: Load topics
        $this->get('/word/random/{topic}', 'ctrl.word:getRandom')->value('topic', null);

        // @TODO
        $this->get('/study/know/{topic}', 'ctrl.study:index')->value('topic', null);
        $this->post('/edge/{word}/{type}', 'ctrl.study:createEdge')->value('type', static::EDGE_KNOW_WORD);
    }
}
