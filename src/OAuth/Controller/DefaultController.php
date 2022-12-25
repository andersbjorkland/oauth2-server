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
        
         $response = await($this->connection->query(InitializeDatabase::getCreateUserTableSQL())
                ->then(
                    function (QueryResult $result) {
                        $this->connection->quit();
                        return Response::plaintext('Success!');   
                    },
                    function (\Exception $exception) {
                        $this->connection->quit();
                        return Response::plaintext('Error! ' . $exception->getMessage());
                    },
                ));
         
         assert($response instanceof ResponseInterface);
         
         return $response;
    }
}