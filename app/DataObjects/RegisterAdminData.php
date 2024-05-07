<?php

declare(strict_types = 1);

namespace App\DataObjects;

class RegisterAdminData
{
    public function __construct(
        public readonly string $name,
        public readonly string $password,
        public readonly \DateTime $birthdate,
        public readonly string $gender,
        public readonly string $address,
        public readonly string $email,
        public readonly string $contact,
        public readonly string $filename,
        public readonly string $storageFilename,

    ) {
    }
}
