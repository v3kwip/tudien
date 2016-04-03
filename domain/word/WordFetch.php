<?php

namespace andytruong\dict\domain\word;

use andytruong\dict\App;
use andytruong\dict\domain\source\SourceRepository;
use andytruong\dict\domain\topic\TopicRepository;

class WordFetch
{
    private $topicRepository;
    private $wordRepository;
    private $sourceRepository;

    public function __construct(
        TopicRepository $topicRepository,
        WordRepository $wordRepository,
        SourceRepository $sourceRepository)
    {
        $this->topicRepository = $topicRepository;
        $this->wordRepository = $wordRepository;
        $this->sourceRepository = $sourceRepository;
    }

    public function fetch($root, $category, $topic, $word, $url)
    {
        $this
            ->registerTopic($root)
            ->registerTopic($category)
            ->registerTopic($topic)
            ->registerWord($word)
            ->registerSource($url)
            ->link(App::HAS_CHILD_TOPIC, $root, $category)
            ->link(App::HAS_CHILD_TOPIC, $category, $topic)
            ->link(App::HAS_TOPIC, $word, $topic)
            ->link(App::HAS_SOURCE, $word, $url);
    }

    private function registerWord($title)
    {
        if (!$this->wordRepository->getId($title)) {
            $this->wordRepository->create($title);
        }

        return $this;
    }

    private function registerTopic($title)
    {
        if (!$this->topicRepository->getId($title)) {
            $this->topicRepository->create($title);
        }

        return $this;
    }

    private function registerSource($url)
    {
        if (!$this->sourceRepository->getId($url)) {
            $this->sourceRepository->create($url);
        }

        return $this;
    }

    private function link($type, $source, $target)
    {
        switch ($type) {
            case App::HAS_CHILD_TOPIC:
                $this->topicRepository->linkSubTopic($source, $target);
                break;

            case App::HAS_TOPIC:
                $this->wordRepository->linkTopic($source, $target);
                break;

            case App::HAS_SOURCE:
                $this->wordRepository->linkSource($source, $target);
                break;
        }

        return $this;
    }
}
