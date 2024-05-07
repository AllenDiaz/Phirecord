<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Hospital;
use App\Entity\HospitalLoginCode;
use App\Contracts\EntityManagerServiceInterface;

class HospitalLoginCodeService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManagerService)
    {
    }

    public function generate(Hospital $hospital): HospitalLoginCode
    {
        $hospitalLoginCode = new HospitalLoginCode();

        $code = random_int(100000, 999999);

        $hospitalLoginCode->setCode((string) $code);
        $hospitalLoginCode->setExpiration(new \DateTime('+10 minutes'));
        $hospitalLoginCode->setHospital($hospital);

        $this->entityManagerService->sync($hospitalLoginCode);

        return $hospitalLoginCode;
    }

    public function verify(Hospital $hospital, string $code): bool
    {
        $hospitalLoginCode = $this->entityManagerService->getRepository(HospitalLoginCode::class)->findOneBy(
            ['hospital' => $hospital, 'code' => $code, 'isActive' => true]
        );

        if (! $hospitalLoginCode) {
            return false;
        }

        if ($hospitalLoginCode->getExpiration() <= new \DateTime()) {
            return false;
        }

        return true;
    }

    public function deactivateAllActiveCodes(Hospital $hospital): void
    {
        $this->entityManagerService->getRepository(HospitalLoginCode::class)
            ->createQueryBuilder('c')
            ->update()
            ->set('c.isActive', '0')
            ->where('c.hospital = :hospital')
            ->andWhere('c.isActive = 1')
            ->setParameter('hospital', $hospital)
            ->getQuery()
            ->execute();
    }
}
