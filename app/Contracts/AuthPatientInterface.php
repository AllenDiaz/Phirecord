<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\Enum\AuthAttemptStatus;
use App\Contracts\PatientInterface;
use App\DataObjects\RegisterPatientData;

interface AuthPatientInterface
{
    public function patient(): ?PatientInterface;

    public function attemptLogin(array $credentials): AuthAttemptStatus;

    public function checkCredentials(PatientInterface $patient, array $credentials): bool;

    public function logOut(): void;

    public function register(RegisterPatientData $data): PatientInterface;

    public function logIn(PatientInterface $patient): void;

    public function attemptTwoFactorLogin(array $data): bool;
}