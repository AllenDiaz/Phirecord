<?php

declare(strict_types = 1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class HospitalLoginRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data, string $path = null): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'password']);
        $v->rule('email', 'email');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
