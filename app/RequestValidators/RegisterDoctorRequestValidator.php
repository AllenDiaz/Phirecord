<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Entity\Doctor;
use Valitron\Validator;
use App\Services\HospitalService;
use App\Exception\ValidationException;
use App\Contracts\RequestValidatorInterface;
use App\Contracts\EntityManagerServiceInterface;

class RegisterDoctorRequestValidator implements RequestValidatorInterface
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
            'required', ['name', 'password', 'confirmPassword', 'hospital',
                         'address', 'email', 'contactNo', 'birthdate', 'sex', 'hospital',
    
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

         $v->rule(
            function($field, $value, $params, $fields) use (&$data) {
                $id = (int) $value;

                if (! $id) {
                    return false;
                }

                $hospital = $this->hospitalService->getById($id);

                if ($hospital) {
                    $data['hospital'] = $hospital;

                    return true;
                }

                return false;
            },
            'hospital'
        )->message('hospital not found');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
