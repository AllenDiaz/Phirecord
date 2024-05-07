<?php

declare(strict_types = 1);

namespace App\DataObjects;

use App\Entity\Doctor;
use App\Entity\Patient;

class CheckupFormData
{
    public function __construct(
        public readonly \DateTime $confineDate,
        public readonly \DateTime $checkupDate,
        public readonly Patient $patient,
        public readonly Doctor $doctor,
        public readonly string $familyMember,
        public readonly \DateTime $menstrualDate,
        public readonly string $fetalHeartTones,
        public readonly string $gravida,
        public readonly string $para,
        public readonly string $labaratory,
        public readonly string $urinalysis,
        public readonly string $bloodCount,
        public readonly string $fecalysis,

    ) {
    }
}
