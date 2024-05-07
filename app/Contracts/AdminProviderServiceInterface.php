<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\DataObjects\RegisterAdminHospitalData;

interface AdminProviderServiceInterface
{
    public function getById(int $adminId): ?AdminInterface;

    public function getByCredentials(array $credentials): ?AdminInterface;

    public function createAdminHospital(RegisterAdminHospitalData $data): void;

    // public function createUser(RegisterUserData $data): AdminInterface;

    // public function verifyUser(AdminInterface $user): void;
}
