<?php

declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Repository\ClientRepository;
use App\Database\Repository\ScopeRepository;
use App\Model\AuthorizationCode;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class AuthorizationCodeManager implements EntityManagerInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly ScopeRepository $scopeRepository,
        private readonly ClientRepository $clientRepository
    ){}

    public function create(mixed $entity): bool
    {
        assert($entity instanceof AuthorizationCode);
        $sql = 'INSERT INTO authorization_code (id, client_id, user_id, expiry_date_time, redirect_uri) VALUES (?, ?, ?, ?, ?)';
        $relationSql = 'INSERT INTO authorization_code_scope (authorization_code_id, scope_id) VALUES (?, ?)';
        
        $result = await(
            $this->connection->query($sql, [
                $entity->getId(), 
                $entity->getClient()->getId(), 
                $entity->getUserIdentifier(),
                $entity->getExpiryDateTime()->format('Y-m-d H:i:s'),
                $entity->getRedirectUri()
            ])->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
                    throw $exception;
                },
        ));
        
        foreach ($entity->getScopes() as $scope) {
            
            $result = await(
                $this->connection->query($relationSql, [
                    $entity->getId(), 
                    $scope->getIdentifier()
                ])->then(
                    function (QueryResult $result) {
                        return true;
                    },
                    function (\Exception $exception) {
                        $this->connection->quit();
                        throw $exception;
                    },
            ));
        }
        
        return true;
    }

    public function update(mixed $entity): bool
    {
        assert($entity instanceof AuthorizationCode);
        
        $sql = 'UPDATE authorization_code SET client_id = ?, user_id = ?, expiry_date_time = ?, redirect_uri = ? WHERE id = ?';
        
        $result = await(
            $this->connection->query($sql, [
                $entity->getClient()->getId(), 
                $entity->getUserIdentifier(),
                $entity->getExpiryDateTime()->format('Y-m-d H:i:s'),
                $entity->getRedirectUri(),
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
        
        // insert-query where not exists
        $insertSql = 'INSERT INTO authorization_code_scope (authorization_code_id, scope_id) SELECT ?, ? WHERE NOT EXISTS (SELECT * FROM authorization_code_scope WHERE authorization_code_id = ? AND scope_id = ?)';
        
        foreach ($entity->getScopes() as $scope) {
            
            $result = await(
                $this->connection->query($insertSql, [
                    $entity->getId(), 
                    $scope->getIdentifier(),
                    $entity->getId(), 
                    $scope->getIdentifier()
                ])->then(
                    function (QueryResult $result) {
                        return true;
                    },
                    function (\Exception $exception) {
                        $this->connection->quit();
                        throw $exception;
                    },
            ));
        }

        // delete-query where exists
        $deleteSql = 'DELETE FROM authorization_code_scope WHERE authorization_code_id = ? AND scope_id NOT IN (?)';
        $currentScopes = array_map(fn ($scope) => $scope->getIdentifier(), $entity->getScopes());
            
        $result = await(
            $this->connection->query($deleteSql, [
                $entity->getId(), 
                $currentScopes
            ])->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
                    throw $exception;
                },
        ));
        
        return true;
    }

    public function delete(mixed $entity): bool
    {
        assert($entity instanceof AuthorizationCode);
        $sql = 'DELETE FROM authorization_code WHERE id = ?';
        $relationSql = 'DELETE FROM authorization_code_scope WHERE authorization_code_id = ?';
        
        $result = await(
            $this->connection->query($relationSql, [
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
        
        $result = await(
            $this->connection->query($sql, [
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
        
        return true;
    }
    
    public function deleteById(string $id): bool
    {
        $sql = 'DELETE FROM authorization_code WHERE id = ?';
        
        $result = await(
            $this->connection->query($sql, [
                $id
            ])->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    $this->connection->quit();
                    throw $exception;
                },
        ));
        
        return true;
    }

    public function get(string $id): mixed
    {
        $sql = 'SELECT * FROM authorization_code WHERE id = ?';
        $relationSql = 'SELECT * FROM authorization_code_scope WHERE authorization_code_id = ?';
        
        $result = await(
            $this->connection->query($sql, [
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
        
        $relationResult = await(
            $this->connection->query($relationSql, [
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
        
        if (empty($result)) {
            return null;
        }
        
        return $this->createAuthorizationCodeFromResultArray($result[0]);
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
    
    public function createAuthorizationCodeFromResultArray(array $result): AuthorizationCode
    {
        $scopeIds = array_map(fn ($row) => $row['scope_id'], $result);
        $scopes = $this->scopeRepository->getScopesByIdentifiers($scopeIds);
        $client = $this->clientRepository->getClientEntity($result['client_id']);
        
        return new AuthorizationCode(
            id: $result[0]['id'],
            expiryDateTime: $result[0]['expiry_date_time'],
            userId: $result[0]['user_id'],
            client: $client,
            redirectUri: $result[0]['redirect_uri'],
            scopes: $scopes
        );
    }

    public function revoke(string $codeId): bool
    {
        return $this->deleteById($codeId);
    }
}