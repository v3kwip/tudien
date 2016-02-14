<?php

use tudien\App;

return call_user_func(function () {
    require_once __DIR__ . '/../vendor/autoload.php';

    $cnf = file_exists(__DIR__ . '/../config.php') ? __DIR__ . '/../config.php' : __DIR__ . '/../config.default.php';
    $app = new App(require $cnf);

    return ('cli' === php_sapi_name()) ? $app : $app->run();
});
