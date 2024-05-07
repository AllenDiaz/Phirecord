<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Entity\Doctor;
use Valitron\Validator;
use App\Services\HospitalService;
use App\Exception\ValidationException;
use App\Contracts\RequestValidatorInterface;
use App\Contracts\EntityManagerServiceInterface;

class RegisterHospitalDoctorRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
    )
    {
    }

    public function validate(array $data, string $path = null): array
    {
        $v = new Validator($data);

        $v->rule(
            'required', ['name', 'password', 'confirmPassword',
                         'address', 'email', 'contactNo', 'birthdate', 'sex', 

        ]);
        $v->rule('lengthMin', 'password', 6);
        $v->rule('email', 'email');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password');
        $v->rule(
            fn($field, $value, $params, $fields) => ! $this->entityManager->getRepository(Doctor::class)->count(
                ['email' => $value]
            ),
            'email'
        )->message('Doctor with the given email address already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
