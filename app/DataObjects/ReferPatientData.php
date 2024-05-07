<?php

declare(strict_types = 1);

namespace App\DataObjects;

use App\Entity\Doctor;
use App\Entity\Patient;

class ReferPatientData
{
    public function __construct(
        public readonly Patient $patient,
        public readonly int $referHospital,
    ) {
    }
}
