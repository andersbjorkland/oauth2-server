<?php

declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Service\UuidGenerator;
use App\Model\Scope;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class ScopeManager implements EntityManagerInterface
{
    
    public function __construct(
        private readonly ConnectionInterface $connection
    ){}

    /**
     * @throws \Throwable
     */
    public function create(mixed $entity): bool
    {
        assert($entity instanceof Scope);
        $sql = 'INSERT INTO scope (id, name, description) VALUES (?, ?, ?)';
        return await($this->connection->query($sql, [
            $entity->getId() ?? UuidGenerator::getCompactUuid4(),
            $entity->getName(), 
            $entity->getDescription()
        ])->then(
            function (QueryResult $result) {
                return true;
            },
            function (\Exception $exception) {
                throw $exception;
            },
        ));
    }

    /**
     * @throws \Throwable
     */
    public function update(mixed $entity): bool
    {
        assert($entity instanceof Scope);
        $sql = 'UPDATE scope SET name = ?, description = ? WHERE id = ?';
        return await($this->connection->query($sql, [$entity->getName(), $entity->getDescription(), $entity->getId()])
            ->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
    }

    /**
     * @throws \Throwable
     */
    public function delete(mixed $entity): bool
    {
        assert($entity instanceof Scope);
        return $this->deleteById($entity->getId());
    }
    
    public function deleteById(string $id): bool
    {
        $sql = 'DELETE FROM scope WHERE id = ?';
        await($this->connection->query($sql, [$id])
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

    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM scope WHERE id = ?';
        $result = await($this->connection->query($sql, [$id])
            ->then(
                function (QueryResult $result) {
                    return $result->resultRows[0] ?? null;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        
        if (empty($result)) {
            return null;
        }
        
        return new Scope(
            name: $result['name'],
            description: $result['description'],
            id: $result['id']
        );
        
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}