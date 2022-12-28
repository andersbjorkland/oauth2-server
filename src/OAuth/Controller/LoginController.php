<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use App\Database\Repository\AccessTokenRepository;
use App\Database\Repository\ClientRepository;
use App\Database\Repository\UserRepository;
use App\OAuth\Validator\UserRequestValidator;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class LoginController
{
    
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AccessTokenRepository $accessTokenRepository,
        private readonly ClientRepository $clientRepository,
    ){}
    
    public function __invoke(RequestInterface $request): ResponseInterface
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
        try {
            $user = $this->userRepository->findByEmail($email);
        } catch (\Throwable $e) {
            $this->userRepository->getConnection()->quit();
            return Response::json(
                ['error' => 'Database failure.']
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
        
        if ($user === null) {
            $this->userRepository->getConnection()->quit();
            return Response::json(
                ['error' => 'Failure: user not found!']
            )->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
        }
        
        if (!password_verify($password, $user->getPassword())) {
            $this->userRepository->getConnection()->quit();
            return Response::json(
                ['error' => 'Failure: invalid password!']
            )->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        try {
            $client = $this->clientRepository->findOneByName('andersbjorkland2.se');
        } catch (\Throwable $e) {
            return Response::json(
                ['error' => 'Database failure.']
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $connection = $this->clientRepository->getConnection();
        $connection->quit();
        
        return Response::json(
            [
                'message' => 'User logged in successfully.',
                'token' => '$accessToken->getIdentifier()',
                'type' => 'Bearer'
            ]
        )->withStatus(StatusCodeInterface::STATUS_OK);
    }

}