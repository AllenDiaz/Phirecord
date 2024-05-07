<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\DataObjects\RegisterPatientData;
use App\DataObjects\RegisterAdminHospitalData;

interface PatientProviderServiceInterface
{
    public function getById(int $patientId): ?PatientInterface;

    public function getByCredentials(array $credentials): ?PatientInterface;

    public function createPatient(RegisterPatientData $data): PatientInterface;
}
