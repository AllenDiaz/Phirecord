<?php

declare(strict_types = 1);

namespace App\DataObjects;

class RegisterHospitalData
{
    public function __construct(
        public readonly string $name,
        public readonly string $address,
        public readonly string $email,
        public readonly string $contactNo,
        public readonly string $password,
        public readonly string $filenameProof,
        public readonly string $storageFilenameProof,
        public readonly string $filenameProfile,
        public readonly string $storageFilenameProfile,
    ) {
    }
}
