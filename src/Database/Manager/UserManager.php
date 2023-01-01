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
                    return true;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));

        return $response;
    }

    /**
     * @throws \Throwable
     */
    public function update(mixed $entity): bool
    {
        assert($entity instanceof User);
        $sql = 'UPDATE user SET email = ?, password = ? WHERE id = ?';
        
        $response = await($this->connection->query($sql, [$entity->getEmail(), $entity->getPassword(), $entity->getId()])
            ->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        
        return $response;
    }

    /**
     * @throws \Throwable
     */
    public function delete(mixed $entity): bool
    {
        assert($entity instanceof User);
        return $this->deleteById($entity->getId());
    }

    /**
     * @throws \Throwable
     */
    public function deleteById(string $id): bool
    {
        $sql = 'DELETE FROM user WHERE id = ?';
        $response = await($this->connection->query($sql, [$id])
            ->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        
        return true;
    }

    /**
     * @param string $id
     * @return User|null
     * @throws \Throwable
     */
    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM user WHERE id = ?';
        
        $response = await($this->connection->query($sql, [$id])
            ->then(
                function (QueryResult $result) {
                    return $result->resultRows;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        
        if (empty($response)) {
            return null;
        }
        
        return $this->createEntity($response[0]);
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
    
    protected function createEntity(array $data): User
    {
        return new User(
            email: $data['email'],
            password: $data['password'],
            id: $data['id'],
            roles: json_decode($data['roles'], true)
        );
    }
}