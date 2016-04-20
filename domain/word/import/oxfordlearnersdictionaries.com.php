<?php

use Symfony\Component\DomCrawler\Crawler;

return call_user_func(function () {
    return [
        '@type -> @ -> .webtop-g span.pos',
        '@pronounces -> .top-container > .top-g -> :first -> .pron-gs -> :each -> .pron-g > div.sound -> @ -> :attr -> data-src-mp3',
        '@means -> #entryContent > div > .h-g > .top-container -> :next-sibling -> :each -> .sn-gs' => [
            '@title -> .shcut',
            '@grammar -> .sn-g .gram-g .gram',
            '@definition -> .sn-g .def',
            '@examples -> .sn-g .x-gs -> :each -> .x-g -> @ -> .x',
        ],
        '@idioms -> #entryContent .idm-gs -> :each -> .idm-g'        => [
            '@title -> .idm',
            '@description -> .sn-gs -> .sn-g .def',
            '@examples -> .sn-gs -> .sn-g .x-gs -> :each -> .x-g -> @ -> .rx-g .x',
            '@geo -> .sn-gs .sn-g .label-g .geo',
            '@tags -> .sn-gs .sn-g .label-g -> :each -> .reg -> @ -> ~',
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
