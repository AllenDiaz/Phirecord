<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Entity\Patient;
use Valitron\Validator;
use App\Services\HospitalService;
use App\Exception\ValidationException;
use App\Contracts\RequestValidatorInterface;
use App\Contracts\EntityManagerServiceInterface;

class RegisterHospitalPatientRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly HospitalService $hospitalService,
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
            fn($field, $value, $params, $fields) => ! $this->entityManager->getRepository(Patient::class)->count(
                ['email' => $value]
            ),
            'email'
        )->message('Patient with the given email address already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
