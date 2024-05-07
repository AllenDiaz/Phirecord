<?php 

declare(strict_types=1);

namespace App\RequestValidators;

use App\Entity\Patient;
use Valitron\Validator;
use App\Services\DoctorService;
use App\Services\PatientService;
use App\Exception\ValidationException;
use App\Contracts\RequestValidatorInterface;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\PatientProviderServiceInterface;
use App\Contracts\HospitalProviderServiceInterface;

class SubmitReferPatientRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly PatientProviderServiceInterface $patientProviderService,
    )
    {
    }

    public function validate(array $data, string $path = null): array
    {
        $v = new Validator($data);

        $v->rule(
            'required', ['referHospital','patient'
        ]);
        
         $v->rule(

            function($field, $value, $params, $fields) use (&$data) {
                $id = (int) $value;

                if (! $id) {
                    return false;
                }

                $patient = $this->patientProviderService->getById($id);

                if ($patient) {
                    $data['patient'] = $patient;

                    return true;
                }

                return false;
            },
            'patient'
        )->message('patient not found');

    
        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
