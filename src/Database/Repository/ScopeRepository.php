<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Model\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class ScopeRepository extends AbstractRepository implements ScopeRepositoryInterface
{

    /**
     * @param $identifier
     * @return Scope|null
     * @throws \Throwable
     */
    public function getScopeEntityByIdentifier($identifier): ?Scope
    {
        $sql = 'SELECT * FROM scope WHERE id = ?';
        
        $result = await($this->connection->query($sql, [$identifier])
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
        
        return self::createScopeFromRow($result[0]);
    }

    /**
     * @param Scope[] $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     * @return Scope[]
     */
    public function finalizeScopes(
        array $scopes, 
        $grantType, 
        ClientEntityInterface $clientEntity, 
        $userIdentifier = null
    ): array
    {
        return [];
    }
    
    public function createScopeFromRow(array $row): Scope
    {
        return new Scope(
            $row['name'],
            $row['description'],
            $row['id']
        );
    }

    /**
     * @param array $scopeIds
     * @return array|Scope[]
     * @throws \Throwable
     */
    public function getScopesByIdentifiers(array $scopeIds): array
    {
        $sql = 'SELECT * FROM scope WHERE id IN (?)';
        
        $result = await($this->connection->query($sql, [$scopeIds])
            ->then(
                function (QueryResult $result) {
                    return $result->resultRows;
                },
                function (\Exception $exception) {
                    throw $exception;
                },
            ));
        
        $scopes = [];
        foreach ($result as $row) {
            $scopes[] = self::createScopeFromRow($row);
        }
        
        return $scopes;
    }
}