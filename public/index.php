<?php

use App\Controllers\IndexController;
use App\Services\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new App\Kernel();

$container = $kernel->getContainer();

/** @var Router $router */
$router = $container->get('router');

$router->add('/', [IndexController::class, 'index']);

$router->run();
