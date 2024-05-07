<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Entity\Doctor;
use Valitron\Validator;
use App\Services\HospitalService;
use App\Exception\ValidationException;
use App\Contracts\RequestValidatorInterface;
use App\Contracts\EntityManagerServiceInterface;

class ChangeAdminPasswordValidator implements RequestValidatorInterface
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
            'required', ['oldPassword', 'newPassword', 'confirmPassword',
    
        ]);
        $v->rule('lengthMin', 'password', 6);
        $v->rule('equals', 'confirmPassword', 'newPassword')->label('Password not match');
    

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }    
}
