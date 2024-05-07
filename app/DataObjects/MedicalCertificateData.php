<?php

declare(strict_types = 1);

namespace App\DataObjects;

use App\Entity\Doctor;
use App\Entity\Patient;

class MedicalCertificateData
{
    public function __construct(
        public readonly \DateTime $certificateDate,
        public readonly Patient $patient,
        public readonly Doctor $doctor,
        public readonly string $impression,
        public readonly string $purpose,
    ) {
    }
}
