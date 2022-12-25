<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Model\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection
    ){}

    /**
     * @throws \Throwable
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity): ?UserEntityInterface
    {
        $userData = await($this->connection->query(
            'SELECT * FROM user WHERE email = ? AND password = ?', 
            [$username, password_hash($password, PASSWORD_DEFAULT)]
        ));
        
        assert($userData instanceof \React\MySQL\QueryResult);
        $this->connection->quit();

        return count($userData->resultRows) > 0 ? $this->constructUser($userData->resultRows[0]) : null;
    }

    /**
     * @throws \Throwable
     */
    public function findByEmail(string $email): ?User
    {
        $userData = await($this->connection->query(
            'SELECT * FROM user WHERE email = ?', 
            [$email]
        ));

        assert($userData instanceof \React\MySQL\QueryResult);
        $this->connection->quit();
        

        return count($userData->resultRows) > 0 ? $this->constructUser($userData->resultRows[0]) : null;
    }
    
    private function constructUser(array $userData): User
    {
        return new User(
            $userData['email'], 
            $userData['password'], 
            $userData['id'], 
            json_decode($userData['roles'], true)
        );
    }
}