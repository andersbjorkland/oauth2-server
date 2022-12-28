<?php

declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Repository\ClientRepository;
use App\Database\Service\UuidGenerator;
use App\Model\Client;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class ClientManager implements EntityManagerInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ){}

    /**
     * @throws \Throwable
     */
    public function create(mixed $entity): bool
    {
        assert($entity instanceof Client);
        $sql = 'INSERT INTO client (id, name, redirect_uri, is_confidential, secret) VALUES (?, ?, ?, ?, ?)';
        return await($this->connection->query($sql, [
            $entity->getId() ?? UuidGenerator::getCompactUuid4(), 
            $entity->getName(), 
            $entity->getRedirectUri(), 
            $entity->isConfidential(), 
            $entity->getSecret()
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
        assert($entity instanceof Client);
        $sql = 'UPDATE client SET name = ?, redirect_uri = ?, is_confidential = ?, secret = ? WHERE id = ?';
        return await($this->connection->query($sql, [
            $entity->getName(), 
            $entity->getRedirectUri(), 
            $entity->isConfidential(), 
            $entity->getSecret(),
            $entity->getId()
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
    public function delete(mixed $entity): bool
    {
        assert($entity instanceof Client);
        $sql = 'DELETE FROM client WHERE id = ?';
        return await($this->connection->query($sql, [$entity->getId()])
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
     * @param string $id
     * @return Client|null
     * @throws \Throwable
     */
    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM client WHERE id = ?';
        return await($this->connection->query($sql, [$id])
            ->then(
                function (QueryResult $result) {
                    $firstResult = $result->resultRows[0] ?? null;
                    return $firstResult ? ClientRepository::createClientFromRow($firstResult) : null;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}