<?php

declare(strict_types=1);

namespace App\OAuth\Validator;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\RequestInterface;
use React\Http\Message\Response;

class UserRequestValidator
{

    public function validateRequest(?object $data): ValidationResponse
    {
        if (empty($data)) {
            return new ValidationResponse(
                false,
                ['error' => 'Expected data.']
            );
        }

        $email = $data->email ?? null;
        $password = $data->password ?? null;
        if ($email === null || $password === null) {
            return new ValidationResponse(
                false,
                ['error' => 'Failure: missing email and/or password!']
            );
        }

        return new ValidationResponse(true);
    }
}