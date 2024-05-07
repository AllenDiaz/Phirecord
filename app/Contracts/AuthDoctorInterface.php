<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\Enum\AuthAttemptStatus;
use App\DataObjects\RegisterDoctorData;

interface AuthDoctorInterface
{
    public function doctor(): ?DoctorInterface;

    public function attemptLogin(array $credentials): AuthAttemptStatus;

    public function checkCredentials(DoctorInterface $doctor, array $credentials): bool;

    public function logOut(): void;

    public function register(RegisterDoctorData $data): DoctorInterface;

    public function logIn(DoctorInterface $doctor): void;

    public function attemptTwoFactorLogin(array $data): bool;
}