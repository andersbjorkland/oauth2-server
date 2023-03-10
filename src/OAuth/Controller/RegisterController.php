<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use App\Database\Manager\ClientManager;
use App\Database\Manager\ScopeManager;
use App\Database\Manager\UserManager;
use App\Database\Service\UuidGenerator;
use App\Model\Client;
use App\Model\Scope;
use App\Model\User;
use App\OAuth\Validator\ClientRequestValidator;
use App\OAuth\Validator\ScopeRequestValidator;
use App\OAuth\Validator\UserRequestValidator;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class RegisterController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly ClientManager $clientManager,
        private readonly ScopeManager $scopeManager
    ){}
    
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $type = $request->getAttribute('type');
        return match ($type) {
            'user' => $this->registerUser($request),
            'client' => $this->registerClient($request),
            'scope' => $this->registerScope($request),
            default => new Response(StatusCodeInterface::STATUS_NOT_FOUND),
        };
    }
    
    public function registerUser(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode((string)$request->getBody());

        $validationResponse = (new UserRequestValidator())->validateRequest($data);
        if (!$validationResponse->isValid()) {
            return Response::json(
                $validationResponse->getErrors()
            )->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $email = $data->email;
        $password = $data->password;
        
        $user = new User(
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT)
        );

        try {
            $result = $this->userManager->create($user);
        } catch (\Throwable $exception) {
            $this->userManager->getConnection()->quit();
            return Response::json(
                [
                    'error' => str_contains($exception->getMessage(), 'Duplicate entry')
                        ? 'Email already exists.'
                        : 'Database failure.'
                ]
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $this->userManager->getConnection()->quit();

        return Response::json(
            ['message' => 'User created successfully.']
        )->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
    
    public function registerClient(RequestInterface $request): ResponseInterface
    {
        $data = json_decode((string)$request->getBody());
        
        $validationResponse = (new ClientRequestValidator())->validateRequest($data);
        if (!$validationResponse->isValid()) {
            return Response::json(
                $validationResponse->getErrors()
            )->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
        }
        
        $name = $data->name;
        $redirectUri = $data->redirect_uri;
        try {
            $secret = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            return Response::json(
                ['error' => 'Failed to generate secret.']
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $client = new Client(
            name: $name,
            redirectUri: $redirectUri,
            secret: $secret
        );
        
        try {
            $result = $this->clientManager->create($client);
        } catch (\Throwable $exception) {
            $this->clientManager->getConnection()->quit();

            return Response::json(
                [
                    'error' => str_contains($exception->getMessage(), 'Duplicate entry')
                        ? 'Name already exists.'
                        : 'Database failure.'
                ]
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $this->clientManager->getConnection()->quit();

        return Response::json(
            ['message' => 'Client created successfully.']
        )->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
    
    public function registerScope(RequestInterface $request): ResponseInterface
    {
        $data = json_decode((string)$request->getBody());
        
        $validationResponse = (new ScopeRequestValidator())->validateRequest($data);
        if (!$validationResponse->isValid()) {
            return Response::json(
                $validationResponse->getErrors()
            )->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
        }
        
        $name = $data->name;
        $description = $data->description ?? null;
        
        $scope = new Scope(
            name: $name,
            description: $description,
            id: UuidGenerator::getCompactUuid4()
        );
        
        try {
            $result = $this->scopeManager->create($scope);
        } catch (\Throwable $exception) {

            $this->scopeManager->getConnection()->quit();

            return Response::json(
                [
                    'error' => str_contains($exception->getMessage(), 'Duplicate entry')
                        ? 'Name already exists.'
                        : 'Database failure.'
                ]
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $this->scopeManager->getConnection()->quit();

        return Response::json(
            ['message' => 'Scope created successfully.']
        )->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
}