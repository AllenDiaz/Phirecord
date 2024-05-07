<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Admin;
use App\Entity\AdminLoginCode;
use App\Contracts\EntityManagerServiceInterface;

class AdminLoginCodeService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManagerService)
    {
    }

    public function generate(Admin $admin): AdminLoginCode
    {
        $adminLoginCode = new AdminLoginCode();

        $code = random_int(100000, 999999);

        $adminLoginCode->setCode((string) $code);
        $adminLoginCode->setExpiration(new \DateTime('+10 minutes'));
        $adminLoginCode->setAdmin($admin);

        $this->entityManagerService->sync($adminLoginCode);

        return $adminLoginCode;
    }

    public function verify(Admin $admin, string $code): bool
    {
        $adminLoginCode = $this->entityManagerService->getRepository(AdminLoginCode::class)->findOneBy(
            ['admin' => $admin, 'code' => $code, 'isActive' => true]
        );

        if (! $adminLoginCode) {
            return false;
        }

        if ($adminLoginCode->getExpiration() <= new \DateTime()) {
            return false;
        }

        return true;
    }

    public function deactivateAllActiveCodes(Admin $admin): void
    {
        $this->entityManagerService->getRepository(AdminLoginCode::class)
            ->createQueryBuilder('c')
            ->update()
            ->set('c.isActive', '0')
            ->where('c.admin = :admin')
            ->andWhere('c.isActive = 1')
            ->setParameter('admin', $admin)
            ->getQuery()
            ->execute();
    }
}
