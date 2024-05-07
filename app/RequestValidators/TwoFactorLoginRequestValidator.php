<?php

declare(strict_types = 1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class TwoFactorLoginRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data, string $file = null): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'code']);
        $v->rule('email', 'email');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
