<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Model\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection
    ){}

    public static function createClientFromRow(array $result): Client
    {
        $client = new Client(
            $result['name'],
            $result['redirect_uri'],
            $result['is_confidential'],
            $result['id']
        );
        return $client;
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
                    $this->connection->quit();
                    return $result->resultRows;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
                    throw $exception;
                },
            ));
        if (count($result) === 0) {
            return null;
        }
        return self::createClientFromRow($result[0]);
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        // TODO: Implement validateClient() method.
    }
}