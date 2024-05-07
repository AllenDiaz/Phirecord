<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\Enum\AuthAttemptStatus;
use App\DataObjects\RegisterAdminData;
use App\DataObjects\RegisterAdminHospitalData;

interface AuthAdminInterface
{
    public function admin(): ?AdminInterface;

    public function attemptLogin(array $credentials): AuthAttemptStatus;

    public function checkCredentials(AdminInterface $admin, array $credentials): bool;

    public function logOut(): void;

    public function register(RegisterAdminData $data): AdminInterface;

    public function registerHospital(RegisterAdminHospitalData $data): void;

    public function logIn(AdminInterface $admin): void;

    public function attemptTwoFactorLogin(array $data): bool;
}