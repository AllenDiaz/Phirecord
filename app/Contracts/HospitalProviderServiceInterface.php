<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\DataObjects\RegisterHospitalData;
use App\DataObjects\RegisterAdminHospitalData;

interface HospitalProviderServiceInterface
{
    public function getById(int $userId): ?HospitalInterface;

    public function getByCredentials(array $credentials): ?HospitalInterface;

    public function createHospital(RegisterHospitalData $data): HospitalInterface;
}
