<?php

use App\OAuth\Controller\DefaultController;
use FrameworkX\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();

$app->get('/', DefaultController::class);

$app->run();