<?php

declare(strict_types = 1);

namespace App\DataObjects;

use App\Entity\Doctor;
use App\Entity\Patient;

class AdmissionFormData
{
    public function __construct(
        public readonly \DateTime $admissionDate,
        public readonly Patient $patient,
        public readonly Doctor $doctor,
        public readonly string $familyMember,
        public readonly string $symptoms,
        public readonly string $bloodPressure,
        public readonly string $temperature,
        public readonly string $weight,
        public readonly string $respiratoryRate,
        public readonly string $pulseRate,
        public readonly string $oxygenSaturation,
        public readonly string $diagnosis,
    ) {
    }
}
