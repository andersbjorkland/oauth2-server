<?php

use App\Config\ContainerConfig;
use App\OAuth\Controller\DefaultController;
use App\OAuth\Controller\LoginController;
use App\OAuth\Controller\RegisterController;
use FrameworkX\App;

require __DIR__ . '/../vendor/autoload.php';

$container = (new ContainerConfig())->loadContainer();

$app = new App($container);

$app->get('/', DefaultController::class);

$app->post('/register/{type}', RegisterController::class);

$app->post('/login', LoginController::class);

$app->run();