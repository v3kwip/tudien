<?php

use Symfony\Component\DomCrawler\Crawler;

return call_user_func(function () {
    return [
        '@type -> @ -> .webtop-g span.pos',
        '@pronounces -> .top-container > .top-g -> :first -> .pron-gs -> :each -> .pron-g > div.sound -> @ -> :attr -> data-src-mp3',
        '@means -> #entryContent -> :each -> .sn-gs'                 => [
            '@title -> .shcut',
            '@items -> :each -> .sn-g' => [
                '@grammar -> .gram-g .gram',
                '@definition -> .def',
                '@examples -> .x-gs -> :each -> .x-g -> @ -> .x',
            ],
        ],
        '@idioms -> #entryContent .idm-gs -> :each -> .idm-g'        => [
            '@idiom -> .idm',
            '@mean -> .sn-gs -> :each -> .sn-g' => [
                '@label -> .label-g .reg',
                '@definition -> .def',
                '@examples -> .x-gs -> :each -> .x-g -> @ -> .rx-g .x',
            ],
        ],
        '@related -> @words -> .nearby > ul -> :each -> li > a -> @' => function (Crawler $node) {
            $url = explode('/', $node->attr('href'));
            $word = array_pop($url);
            $word = explode('#', $word)[0];
            $word = explode('_', $word)[0];

            return $word;
        },
        '@related -> @idioms -> #relatedentries -> :each -> dl > dd > ul > li > a > .arl8 -> @ -> ~',
    ];
});
