<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use App\Database\Manager\UserManager;
use App\Model\User;
use App\OAuth\Validator\UserRequestValidator;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class RegisterController
{
    public function __construct(
        private readonly UserManager $userManager,
    ){}
    
    public function __invoke(ServerRequestInterface $request): ResponseInterface
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
            return Response::json(
                [
                    'error' => str_contains($exception->getMessage(), 'Duplicate entry')
                        ? 'Email already exists.'
                        : 'Database failure.'
                ]
            )->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
        
        return Response::json(
            ['message' => 'User created successfully.']
        )->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
}