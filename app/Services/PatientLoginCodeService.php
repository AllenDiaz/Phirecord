<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Patient;
use App\Entity\PatientLoginCode;
use App\Contracts\EntityManagerServiceInterface;

class PatientLoginCodeService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManagerService)
    {
    }

    public function generate(Patient $patient): PatientLoginCode
    {
        $patientLoginCode = new PatientLoginCode();

        $code = random_int(100000, 999999);

        $patientLoginCode->setCode((string) $code);
        $patientLoginCode->setExpiration(new \DateTime('+10 minutes'));
        $patientLoginCode->setPatient($patient);

        $this->entityManagerService->sync($patientLoginCode);

        return $patientLoginCode;
    }

    public function verify(Patient $patient, string $code): bool
    {
        $patientLoginCode = $this->entityManagerService->getRepository(PatientLoginCode::class)->findOneBy(
            ['patient' => $patient, 'code' => $code, 'isActive' => true]
        );

        if (! $patientLoginCode) {
            return false;
        }

        if ($patientLoginCode->getExpiration() <= new \DateTime()) {
            return false;
        }

        return true;
    }

    public function deactivateAllActiveCodes(Patient $patient): void
    {
        $this->entityManagerService->getRepository(PatientLoginCode::class)
            ->createQueryBuilder('c')
            ->update()
            ->set('c.isActive', '0')
            ->where('c.patient = :patient')
            ->andWhere('c.isActive = 1')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->execute();
    }
}
