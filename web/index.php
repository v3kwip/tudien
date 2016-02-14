<?php

use tudien\App;

return call_user_func(function () {
    $conf = file_exists(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.default.php';
    $app = new App(require $conf);
    $app->run();
});
