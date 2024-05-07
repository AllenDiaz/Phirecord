<?php

declare(strict_types = 1);

namespace App\DataObjects;

use App\Entity\Hospital;

class RegisterDoctorData
{
    public function __construct(
        public readonly string $name,
        public readonly string $password,
        public readonly \DateTime $birthDate,
        public readonly string $sex,
        public readonly string $address,
        public readonly Hospital $hospital,
        public readonly string $email,
        public readonly string $contactNo,
        public readonly string $idFilename,
        public readonly string $storageIdFilename,
        public readonly string $empFilename,
        public readonly string $StorageEmpFilename,
    ) {
    }
}
