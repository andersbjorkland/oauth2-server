<?php

use App\Database\Manager\EntityManagerInterface;
use App\Database\Manager\UserManager;
use App\OAuth\Controller\DefaultController;
use App\OAuth\Controller\RegisterController;
use Dotenv\Dotenv;
use FrameworkX\App;
use FrameworkX\Container;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container([
    ConnectionInterface::class => function (string $MYSQL_URI) {
        return (new Factory())->createLazyConnection($MYSQL_URI);
    },
    UserManager::class => fn(ConnectionInterface $connection) => new UserManager($connection),
]);

$app = new App($container);

$app->get('/', DefaultController::class);

// register new user
$app->post('/register', RegisterController::class);

$app->run();