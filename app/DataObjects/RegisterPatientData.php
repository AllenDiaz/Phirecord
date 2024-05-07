<?php

declare(strict_types = 1);

namespace App\DataObjects;

use App\Entity\Hospital;

class RegisterPatientData
{
    public function __construct(
         public readonly string $name,
        public readonly string $address,
        public readonly string $email,
        public readonly string $contactNo,
        public readonly string $password,
        public readonly Hospital $hospital,
        public readonly string $philhealthNo,
        public readonly string $contactGuard,
        public readonly string $guardianName,
        public readonly \DateTime $birthDate,
        public readonly string $sex,
        public readonly string $idFilename,
        public readonly string $idStorageFilename,
    ) {
    }
}
