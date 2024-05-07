<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorInterface;
use App\Entity\Admin;
use App\Exception\ValidationException;
use Valitron\Validator;

class RegisterAdminRequestValidator implements RequestValidatorInterface
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {
    }

    public function validate(array $data, string $path = null): array
    {
        $v = new Validator($data);

        $v->rule(
            'required', ['name', 'password', 'confirmPassword', 'birthdate', 'gender',
                         'address', 'email', 'contact'
        ]);
        $v->rule('lengthMin', 'password', 6);
        $v->rule('email', 'email');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password');
        $v->rule(
            fn($field, $value, $params, $fields) => ! $this->entityManager->getRepository(Admin::class)->count(
                ['email' => $value]
            ),
            'email'
        )->message('Admin with the given email address already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
