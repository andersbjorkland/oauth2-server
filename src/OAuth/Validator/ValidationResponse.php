<?php

declare(strict_types=1);

namespace App\OAuth\Validator;

class ValidationResponse
{
    public function __construct(
        private readonly bool $isValid,
        private readonly array $errors = []
    ){}

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    

}