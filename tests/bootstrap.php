<?php

// autoload
use React\MySQL\Factory;

require __DIR__ . '/../vendor/autoload.php';

// load env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env.test');
$dotenv->load();

try {
    $connection = (new Factory())->createLazyConnection($_ENV['MYSQL_URI']);
    $dbInitializer = new \App\Database\Service\Initializer($connection);
    $dbInitializer->init();
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
} catch (Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
}