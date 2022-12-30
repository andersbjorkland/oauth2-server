<?php

use App\Database\Manager\AccessTokenManager;
use App\Database\Manager\AuthorizationCodeManager;
use App\Database\Manager\UserManager;
use App\Database\Repository\AccessTokenRepository;
use App\Database\Repository\AuthorizationCodeRepository;
use App\Database\Repository\ClientRepository;
use App\Database\Repository\ScopeRepository;
use App\Database\Repository\UserRepository;
use App\OAuth\Controller\DefaultController;
use App\OAuth\Controller\LoginController;
use App\OAuth\Controller\RegisterController;
use Dotenv\Dotenv;
use FrameworkX\App;
use FrameworkX\Container;
use League\OAuth2\Server\AuthorizationServer;
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
    UserRepository::class => fn(ConnectionInterface $connection) => new UserRepository($connection),
    ScopeRepository::class => fn(ConnectionInterface $connection) => new ScopeRepository($connection),
    ClientRepository::class => fn(ConnectionInterface $connection) => new ClientRepository($connection),
    AccessTokenManager::class => fn(
        ConnectionInterface $connection, 
        ClientRepository $clientRepository
    ) => new AccessTokenManager($connection, $clientRepository),
    AccessTokenRepository::class => fn (
        AccessTokenManager $accessTokenManager
    ) => new AccessTokenRepository($accessTokenManager),
    AuthorizationCodeManager::class => 
        fn (
            ConnectionInterface $connection, 
            ScopeRepository $scopeRepository, 
            ClientRepository $clientRepository
        ) => new AuthorizationCodeManager($connection, $scopeRepository, $clientRepository),
    AuthorizationCodeRepository::class => fn (
        AuthorizationCodeManager $authorizationCodeManager
    ) => new AuthorizationCodeRepository($authorizationCodeManager),
    AuthorizationServer::class => function (
        ClientRepository $clientRepository, 
        ScopeRepository $scopeRepository, 
        AccessTokenRepository $accessTokenRepository
    ) {
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            file_get_contents(__DIR__ . '/../security/private.key'),
            file_get_contents(__DIR__ . '/../security/defuse.key')
        );
        
        return $server;
    }
    
]);

$app = new App($container);

$app->get('/', DefaultController::class);

// register new user
$app->post('/register/{type}', RegisterController::class);

$app->post('/login', LoginController::class);

$app->run();