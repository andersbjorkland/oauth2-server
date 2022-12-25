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
        $sql = 'INSERT INTO client (id, name, redirect_uri, is_confidential) VALUES (?, ?, ?, ?)';
        return await($this->connection->query($sql, [$entity->getId() ?? UuidGenerator::getCompactUuid4(), $entity->getName(), $entity->getRedirectUri(), $entity->isConfidential()])
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
    }

    /**
     * @throws \Throwable
     */
    public function update(mixed $entity): bool
    {
        assert($entity instanceof Client);
        $sql = 'UPDATE client SET name = ?, redirect_uri = ?, is_confidential = ? WHERE id = ?';
        return await($this->connection->query($sql, [$entity->getName(), $entity->getRedirectUri(), $entity->isConfidential(), $entity->getId()])
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
                    $this->connection->quit();
                    return true;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
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
                    $this->connection->quit();
                    $firstResult = $result->resultRows[0] ?? null;
                    return $firstResult ? ClientRepository::createClientFromRow($firstResult) : null;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
                    throw $exception;
                },
            ));
    }
}