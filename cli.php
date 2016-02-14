<?php

use Symfony\Component\Console\Application as Console;
use tudien\App;

return call_user_func(function () {
    $console = new Console(App::NAME, App::VERSION);

    /** @var App $app */
    $app = require_once __DIR__ . '/web/index.php';

    # …
    # $db = $app['dbs']['default'];

    foreach ($app->keys() as $service) {
        if (false !== strpos($service, 'cmd.')) {
            $console->add($app[$service]);
        }
    }

    $console->run();
});
