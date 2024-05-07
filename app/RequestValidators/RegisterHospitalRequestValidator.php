<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Entity\Admin;
use Valitron\Validator;
use App\Entity\Hospital;
use App\Exception\ValidationException;
use App\Contracts\RequestValidatorInterface;
use App\Contracts\EntityManagerServiceInterface;

class RegisterHospitalRequestValidator implements RequestValidatorInterface
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {
    }

    public function validate(array $data, string $path = null): array
    {
        $v = new Validator($data);

        $v->rule(
            'required', ['name', 'password', 'confirmPassword',
                         'address', 'email', 'contactNo'
        ]);
        $v->rule('lengthMin', 'password', 6);
        $v->rule('email', 'email');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password');
        $v->rule(
            fn($field, $value, $params, $fields) => ! $this->entityManager->getRepository(Hospital::class)->count(
                ['email' => $value]
            ),
            'email'
        )->message('Hospital with the given email address already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
