<?php

declare(strict_types=1);

namespace App\OAuth\Validator;

class ClientRequestValidator implements ValidatorInterface
{

    public function validateRequest(?object $data): ValidationResponse
    {
        if (empty($data)) {
            return new ValidationResponse(
                false,
                ['error' => 'Expected data.']
            );
        }

        $name = $data->name ?? null;
        $redirectUri = $data->redirect_uri ?? null;
        if ($name === null || $redirectUri === null) {
            return new ValidationResponse(
                false,
                ['error' => 'Failure: missing name and/or redirect_uri!']
            );
        }

        return new ValidationResponse(true);
    }
}