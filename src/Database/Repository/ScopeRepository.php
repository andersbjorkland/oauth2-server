<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Model\Scope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{

    public function getScopeEntityByIdentifier($identifier): ?Scope
    {
        return null;
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
}