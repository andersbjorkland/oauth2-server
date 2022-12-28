<?php

declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Repository\ClientRepository;
use App\Database\Service\UuidGenerator;
use App\Model\AccessToken;
use League\OAuth2\Server\CryptKey;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class AccessTokenManager implements EntityManagerInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly ClientRepository $clientRepository,
    ){}

    /**
     * @throws \Throwable
     */
    public function create(mixed $entity): bool
    {
        assert($entity instanceof AccessToken);
        $sql = 'INSERT INTO access_token (id, client_id, user_id, scopes, expiry_date_time) VALUES (?, ?, ?, ?, ?)';
        $result = await($this->connection->query($sql, [
            $entity->getId() ?? UuidGenerator::getCompactUuid4(), 
            $entity->getClient()->getId(), 
            $entity->getUserIdentifier(),
            json_encode($entity->getScopes()), 
            $entity->getExpiryDateTime()->format('Y-m-d H:i:s')
        ])->then(
            function (QueryResult $result) {
                return true;
            },
            function (\Exception $exception) {
                $this->connection->quit();
                throw $exception;
            },
        ));
        
        $this->connection->quit();
        
        return $result;
    }

    public function update(mixed $entity): bool
    {
        return false;
    }

    public function delete(mixed $entity): bool
    {
        assert($entity instanceof AccessToken);
        $sql = 'DELETE FROM access_token WHERE id = ?';
        $result = await($this->connection->query($sql, [
            $entity->getId()
        ])->then(
            function (QueryResult $result) {
                return true;
            },
            function (\Exception $exception) {
                $this->connection->quit();
                throw $exception;
            },
        ));
        
        $this->connection->quit();
        
        return $result;
    }

    /**
     * @param string $id
     * @return ?AccessToken
     * @throws \Throwable
     */
    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM access_token WHERE id = ?';
        $result = await($this->connection->query($sql, [
            $id
        ])->then(
            function (QueryResult $result) {
                return $result->resultRows;
            },
            function (\Exception $exception) {
                $this->connection->quit();
                throw $exception;
            },
        ));
        
        $this->connection->quit();
        
        if (count($result) === 0) {
            return null;
        }
        
        $row = $result[0];
        $scopes = json_decode($row['scopes'], true);
        $expiryDateTime = new \DateTimeImmutable($row['expiry_date_time']);
        $client = $this->clientRepository->getClientEntity($row['client_id']);
        $privateKey = new CryptKey(__DIR__.'/../../../security/private.key', null, false);
        return new AccessToken(
            id: $row['id'],
            expiryDateTime: $expiryDateTime,
            userIdentifier: $row['user_id'],
            client: $client,
            privateKey: $privateKey,
            scopes: $scopes
        );
    }

    /**
     * @throws \Throwable
     */
    public function revoke(string $tokenID): bool
    {
        $sql = 'DELETE FROM access_token WHERE id = ?';
        $result = await($this->connection->query($sql, [
            $tokenID
        ])->then(
            function (QueryResult $result) {
                return true;
            },
            function (\Exception $exception) {
                $this->connection->quit();
                throw $exception;
            },
        ));
        
        $this->connection->quit();
        
        return $result;
    }

    /**
     * @throws \Throwable
     */
    public function isRevoked(string $tokenId): bool
    {
        return $this->get($tokenId) === null;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
    
    
}