<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Doctor;
use App\Entity\DoctorLoginCode;
use App\Contracts\EntityManagerServiceInterface;

class DoctorLoginCodeService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManagerService)
    {
    }

    public function generate(Doctor $doctor): DoctorLoginCode
    {
        $doctorLoginCode = new DoctorLoginCode();

        $code = random_int(100000, 999999);

        $doctorLoginCode->setCode((string) $code);
        $doctorLoginCode->setExpiration(new \DateTime('+10 minutes'));
        $doctorLoginCode->setDoctor($doctor);

        $this->entityManagerService->sync($doctorLoginCode);

        return $doctorLoginCode;
    }

    public function verify(Doctor $doctor, string $code): bool
    {
        $doctorLoginCode = $this->entityManagerService->getRepository(DoctorLoginCode::class)->findOneBy(
            ['doctor' => $doctor, 'code' => $code, 'isActive' => true]
        );

        if (! $doctorLoginCode) {
            return false;
        }

        if ($doctorLoginCode->getExpiration() <= new \DateTime()) {
            return false;
        }

        return true;
    }

    public function deactivateAllActiveCodes(Doctor $doctor): void
    {
        $this->entityManagerService->getRepository(DoctorLoginCode::class)
            ->createQueryBuilder('c')
            ->update()
            ->set('c.isActive', '0')
            ->where('c.doctor = :doctor')
            ->andWhere('c.isActive = 1')
            ->setParameter('doctor', $doctor)
            ->getQuery()
            ->execute();
    }
}
