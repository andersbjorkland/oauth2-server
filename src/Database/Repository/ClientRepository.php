<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Model\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class ClientRepository extends AbstractRepository implements ClientRepositoryInterface
{
    public static function createClientFromRow(array $result): Client
    {
        $client = new Client(
            name: $result['name'],
            redirectUri:  $result['redirect_uri'],
            isConfidential: $result['is_confidential'] === 1,
            id: $result['id']
        );
        return $client;
    }

    /**
     * @throws \Throwable
     */
    public function findOneByName(string $name): ?ClientEntityInterface
    {
        $sql = 'SELECT * FROM client WHERE name = ? LIMIT 1';
        
        $result = await($this->connection->query($sql, [$name])
            ->then(
                function (QueryResult $result) {
                    return $result;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        
        assert($result instanceof QueryResult);

        $rows = $result->resultRows;
        
        if (count($rows) === 0) {
            return null;
        }
        
        return self::createClientFromRow($rows[0]);
    }

    /**
     * @throws \Throwable
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $sql = 'SELECT * FROM client WHERE id = ?';
        $result = await($this->connection->query($sql, [$clientIdentifier])
            ->then(
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
        return self::createClientFromRow($result[0]);
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $sql = 'SELECT * FROM client WHERE id = ? AND secret = ?';
        $result = await($this->connection->query($sql, [$clientIdentifier, $clientSecret])
            ->then(
                function (QueryResult $result) {
                    return $result->resultRows;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        if (count($result) === 0) {
            return false;
        }
        return true;
    }
}