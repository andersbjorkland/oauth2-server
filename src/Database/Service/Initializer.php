<?php

declare(strict_types=1);

namespace App\Database\Service;

use React\MySQL\ConnectionInterface;
use function React\Async\await;

class Initializer
{
    public function __construct(
        private ConnectionInterface $connection
    ){}

    /**
     * @throws \Throwable
     */
    public function init(): bool
    {
        // create user table
        await($this->connection->query(InitializeDatabase::getCreateUserTableSQL()));
        
        // create client table
        await($this->connection->query(InitializeDatabase::getCreateClientTableSQL()));

        // create scope table
        await($this->connection->query(InitializeDatabase::getCreateScopeTableSQL()));
        
        // create access token table
        await($this->connection->query(InitializeDatabase::getCreateAccessTokenTableSQL()));

        // create authorization code table
        await($this->connection->query(InitializeDatabase::getCreateAuthorizationCodeTableSQL()));

        // create refresh token table
        await($this->connection->query(InitializeDatabase::getCreateRefreshTokenTableSQL()));
        
        return true;
    }
    
}