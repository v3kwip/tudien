<?php

use andytruong\dict\App;
use Symfony\Component\Console\Application;

return call_user_func(function () {
    require_once __DIR__ . '/vendor/autoload.php';

    $cnf = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.default.php';
    $app = new App(require $cnf);
    $app['console']->run();
});
