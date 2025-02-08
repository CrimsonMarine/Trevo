<?php

use app\classes\GlobalMiddleware;
use app\classes\SessionMiddleware;
use Slim\Factory\AppFactory;

$routes = require '../app/routes/router.php';

$app = AppFactory::create();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$app->add(GlobalMiddleware::class);
$app->add(SessionMiddleware::class);

$routes($app);

$app->run();