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
use App\Contracts\DoctorProviderServiceInterface;
use App\Contracts\PatientProviderServiceInterface;

class SubmitAdmissionFormRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly PatientProviderServiceInterface $patientProviderService,
        private readonly DoctorProviderServiceInterface $doctorProviderService,
    )
    {
    }

    public function validate(array $data, string $path = null): array
    {
        $v = new Validator($data);

        $v->rule(
            'required', ['admissionDate', 'familyMember', 'symptoms',
                         'bloodPressure', 'temperature', 'weight', 'respiratoryRate', 'pulseRate',
                         'oxygenSaturation', 'diagnosis', 'diagnosis', 'patient', 'doctor'
        ]);
        $v->rule('dateBefore', 'dateAdmission', new \DateTime());
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

        $v->rule(
            function($field, $value, $params, $fields) use (&$data) {
                $id = (int) $value;

                if (! $id) {
                    return false;
                }

                $doctor = $this->doctorProviderService->getById($id);

                if ($doctor) {
                    $data['doctor'] = $doctor;

                    return true;
                }

                return false;
            },
            'doctor'
        )->message('doctor not found');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
