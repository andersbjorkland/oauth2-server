<?php

declare(strict_types=1);

namespace App\Database\Repository;

use React\MySQL\ConnectionInterface;

abstract class AbstractRepository
{
    public function __construct(
        protected readonly ConnectionInterface $connection
    ){}
    
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}