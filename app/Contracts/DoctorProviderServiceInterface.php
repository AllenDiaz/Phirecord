<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\DataObjects\RegisterDoctorData;
use App\DataObjects\RegisterAdminHospitalData;

interface DoctorProviderServiceInterface
{
    public function getById(int $doctorId): ?DoctorInterface;

    public function getByCredentials(array $credentials): ?DoctorInterface;

    public function createDoctor(RegisterDoctorData $data): DoctorInterface;
}
