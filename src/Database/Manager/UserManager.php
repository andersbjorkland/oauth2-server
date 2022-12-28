<?php

declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Service\UuidGenerator;
use App\Model\User;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class UserManager implements EntityManagerInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection
    ){}

    /**
     * @throws \Throwable
     */
    public function create(mixed $entity): bool
    {
        assert($entity instanceof User);
        if (empty($entity->getId())) {
            $entity->setId(UuidGenerator::getCompactUuid4());
        }
        $sql = 'INSERT INTO user (id, email, password, roles) VALUES (?, ?, ?, ?)';
        $response = await($this->connection->query($sql, 
            [
                $entity->getId(), 
                $entity->getEmail(), 
                $entity->getPassword(),
                json_encode($entity->getRoles())
            ])
            ->then(
                function (QueryResult $result) {
                    $this->connection->quit();
                    return true;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
                    throw $exception;
                },
            ));

        return $response;
    }

    public function update(mixed $entity): bool
    {
        assert($entity instanceof User);
        $sql = 'UPDATE user SET email = ?, password = ? WHERE id = ?';
        try {
            $response = await($this->connection->query($sql, [$entity->getEmail(), $entity->getPassword(), $entity->getId()])
                ->then(
                    function (QueryResult $result) {
                        $this->connection->quit();
                        return true;
                    },
                    function (\Exception $exception) {
                        $this->connection->quit();
                        return false;
                    },
                ));
        } catch (\Throwable $e) {
            return false;
        }
        
        return $response;
    }

    public function delete(mixed $entity): bool
    {
        assert($entity instanceof User);
        $sql = 'DELETE FROM user WHERE id = ?';
        try {
            $response = await($this->connection->query($sql, [$entity->getId()])
                ->then(
                    function (QueryResult $result) {
                        $this->connection->quit();
                        return true;
                    },
                    function (\Exception $exception) {
                        $this->connection->quit();
                        return false;
                    },
                ));
        } catch (\Throwable $e) {
            return false;
        }
        
        return $response;
    }

    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM user WHERE id = ?';
        try {
            $response = await($this->connection->query($sql, [$id])
                ->then(
                    function (QueryResult $result) {
                        $this->connection->quit();
                        return $result->resultRows[0];
                    },
                    function (\Exception $exception) {
                        $this->connection->quit();
                        return null;
                    },
                ));
        } catch (\Throwable $e) {
            return null;
        }
        
        return $response;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}