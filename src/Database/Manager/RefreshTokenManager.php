<?php

declare(strict_types=1);

namespace App\Database\Manager;

use App\Model\RefreshToken;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class RefreshTokenManager implements EntityManagerInterface
{

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly AccessTokenManager  $accessTokenManager
    ) {}

    /**
     * @throws \Throwable
     */
    public function create(mixed $entity): bool
    {
        assert($entity instanceof RefreshToken);
        
        $sql = 'INSERT INTO refresh_token (access_token_id, id, expiry_date_time) VALUES (?, ?, ?)';
        
        $result = await($this->connection->query($sql,
            [
                $entity->getAccessToken()->getIdentifier() ?? null,
                $entity->getIdentifier(),
                $entity->getExpiryDateTime()->format('Y-m-d H:i:s')
            ]
        ))->then(
            function (QueryResult $result) {
                return true;
            },
            function (\Exception $exception) {
                throw $exception;
            },
        );
        
        return $result;
    }

    /**
     * @throws \Throwable
     */
    public function update(mixed $entity): bool
    {
        assert($entity instanceof RefreshToken);
        
        $sql = 'UPDATE refresh_token SET access_token_id = ?, expiry_date_time = ? WHERE id = ?';
        
        $result = await($this->connection->query($sql,
            [
                $entity->getAccessToken()->getIdentifier(),
                $entity->getExpiryDateTime()->format('Y-m-d H:i:s'),
                $entity->getIdentifier()
            ]
        ))->then(
            function (QueryResult $result) {
                return true;
            },
            function (\Exception $exception) {
                throw $exception;
            },
        );
        
        return $result;
    }

    /**
     * @throws \Throwable
     */
    public function delete(mixed $entity): bool
    {
        assert($entity instanceof RefreshToken);
        return $this->deleteById($entity->getIdentifier());
    }

    /**
     * @throws \Throwable
     */
    public function deleteById(string $id): bool
    {
        $sql = 'DELETE FROM refresh_token WHERE id = ?';
        
        await($this->connection->query($sql, [$id])->then(
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
     * @throws \Throwable
     * @param string $id
     * @return RefreshToken|null
     */
    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM refresh_token WHERE id = ?';
        
        $result = await($this->connection->query($sql, [$id])->then(
            function (QueryResult $result) {
                return $result->resultRows;
            },
            function (\Exception $exception) {
                throw $exception;
            },
        ));
        
        if (count($result) === 0) {
            return null;
        }

        return $this->createRefreshToken($result[0]);
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @throws \Throwable
     */
    protected function createRefreshToken(array $row): RefreshToken
    {
        return new RefreshToken(
            id: $row['id'],
            expiryDateTime: new \DateTimeImmutable($row['expiry_date_time']),
            accessToken: $this->accessTokenManager->get($row['access_token_id'])
        );
    }

    /**
     * @throws \Throwable
     */
    public function isRevoked(string $tokenId): bool
    {
        $sql = 'SELECT * FROM refresh_token WHERE id = ? AND expiry_date_time > NOW()';
        
        $result = await($this->connection->query($sql, [$tokenId])->then(
            function (QueryResult $result) {
                return $result->resultRows;
            },
            function (\Exception $exception) {
                throw $exception;
            },
        ));
        
        return count($result) === 0;
    }
}