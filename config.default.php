<?php

return call_user_func(function () {
    return [
        'db.options'      => [
            'driver' => 'pdo_sqlite',
            'path'   => __DIR__ . '/db.sqlite',
        ],
        'orm.proxies_dir' => __DIR__ . '/files/cache/doctrine/orm/proxy',
        'orm.em.options'  => [
            'mappings' => [
                [
                    'type'      => 'annotation',
                    'namespace' => 'tudien\entity',
                    'path'      => __DIR__ . '/entity',
                ]
            ],
        ],
    ];
});
