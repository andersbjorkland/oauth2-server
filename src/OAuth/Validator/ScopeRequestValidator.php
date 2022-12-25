<?php

declare(strict_types=1);

namespace App\OAuth\Validator;

class ScopeRequestValidator implements ValidatorInterface
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
        if ($name === null) {
            return new ValidationResponse(
                false,
                ['error' => 'Failure: missing required name!']
            );
        }

        return new ValidationResponse(true);
    }
}