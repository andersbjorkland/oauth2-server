<?php

namespace App\OAuth\Validator;

interface ValidatorInterface
{
    public function validateRequest(?object $data): ValidationResponse;
}