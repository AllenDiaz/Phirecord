<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\Enum\AuthAttemptStatus;
use App\DataObjects\RegisterHospitalData;

interface AuthHospitalInterface
{
    public function hospital(): ?HospitalInterface;

    public function attemptLogin(array $credentials): AuthAttemptStatus;

    public function checkCredentials(HospitalInterface $hospital, array $credentials): bool;

    public function logOut(): void;

    public function register(RegisterHospitalData $data): HospitalInterface;

    public function logIn(HospitalInterface $hospital): void;

    public function attemptTwoFactorLogin(array $data): bool;
}