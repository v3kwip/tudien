<?php

namespace andytruong\dict;

use Composer\Autoload\ClassLoader;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Helper\HelperSet;

return call_user_func(function () {
    global $autoloadFile;

    /** @var ClassLoader $loader */
    $loader = require $autoloadFile;
    $loader->addPsr4('andytruong\\dict\\', __DIR__);

    /** @var App $app */
    $app = require_once __DIR__ . '/public/index.php';
    $db = $app['dbs']['default'];

    return new HelperSet(['db' => new ConnectionHelper($db)]);
});
