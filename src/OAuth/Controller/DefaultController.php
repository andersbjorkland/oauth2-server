<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use App\Database\Service\InitializeDatabase;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use function React\Async\await;

class DefaultController
{
    public function __construct(
        private ConnectionInterface $connection
    ){}
    
    public function __invoke(): ResponseInterface
    {
        $errorMessage = null;
        $successMessage = null;
        $createClientSuccess = await($this->connection->query(InitializeDatabase::getCreateClientTableSQL())
            ->then(
                function (QueryResult $result) {
                    return true;
                },
                function (\Exception $exception) {
                    return false;
                },
            ));
        assert(is_bool($createClientSuccess));
        
         $createUserSuccess = await($this->connection->query(InitializeDatabase::getCreateUserTableSQL())
                ->then(
                    function (QueryResult $result) {
                        return true;
                    },
                    function (\Exception $exception) {
                        return false;
                    },
                ));
        assert(is_bool($createUserSuccess));
        
        $createScopeSuccess = await($this->connection->query(InitializeDatabase::getCreateScopeTableSQL())
                ->then(
                    function (QueryResult $result) {
                        return true;
                    },
                    function (\Exception $exception) {
                        return false;
                    },
                ));
        assert(is_bool($createScopeSuccess));
        
        $createAccessTokenSuccess = await($this->connection->query(InitializeDatabase::getCreateAccessTokenTableSQL())
                ->then(
                    function (QueryResult $result) {
                        return true;
                    },
                    function (\Exception $exception) {
                        return false;
                    },
                ));
        assert(is_bool($createAccessTokenSuccess));

        $this->connection->quit();

        return Response::json([
            'createdUserTable' => $createUserSuccess,
            'createdClientTable' => $createClientSuccess,
            'createdScopeTable' => $createScopeSuccess,
            'createdAccessTokenTable' => $createAccessTokenSuccess,
        ]);
    }
}