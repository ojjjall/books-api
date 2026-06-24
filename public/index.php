<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use App\Middleware\JsonBodyParser;
use App\Middleware\Cors;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$app = AppFactory::create();

$app->add(new JsonBodyParser());
$app->add(new Cors());
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

(require __DIR__ . '/../src/routes.php')($app);

$app->run();