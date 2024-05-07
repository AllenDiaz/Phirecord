<?php

declare(strict_types = 1);

namespace App\Services;

use App\Enum\Status;
use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Contracts\AdminInterface;
use App\Contracts\SessionInterface;
use App\DataObjects\DataTableQueryParams;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\AdminProviderServiceInterface;
use App\Contracts\EntityManagerServiceInterface;

class AdminService
{
public function __construct(
                private readonly EntityManagerServiceInterface $entityManagerService,
                private readonly SessionInterface $session,
                private readonly AdminProviderServiceInterface $adminProviderService
                )
    {
    }

    public function getPaginatedRegisteredHospital (DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManagerService
            ->getRepository(Hospital::class)
            ->createQueryBuilder('h')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('h.status = :status and h.isArchived = :archive')
            ->setParameter(':status', Status::Approved)
            ->setParameter(':archive', FALSE);
        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contactNumber', 'address', 
            'approvedAt', 'hospitalStorageFilename', 'storageFilename', 'createdAt'
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('h.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('h.' . $orderBy, $orderDir);

        return new Paginator($query);
    }
    
    public function approvedHospitalCount()
    {
          $qb =  $this->entityManagerService
                    ->getRepository(Hospital::class)
                    ->createQueryBuilder('h')
                    ->where('h.status = :status and h.isArchived = :archive')
                    ->setParameter('status', Status::Approved)
                    ->setParameter('archive', FALSE);

        $hospital = $qb->getQuery()->getScalarResult();
        
        return $hospitalCount = sizeof($hospital);
    }
    
    public function approvedDoctorCount()
    {
          $qb =  $this->entityManagerService
                    ->getRepository(Doctor::class)
                    ->createQueryBuilder('d')
                    ->where('d.status = :status and d.isArchived = :archive')
                    ->setParameter('status', Status::Approved)
                    ->setParameter('archive', FALSE);

        $doctor = $qb->getQuery()->getScalarResult();
        
        return $doctorCount = sizeof($doctor);
    }
    
    public function approvedPatientCount()
    {
          $qb =  $this->entityManagerService
                    ->getRepository(Patient::class)
                    ->createQueryBuilder('p')
                    ->where('p.status = :status and p.isArchived = :archive')
                    ->setParameter('status', Status::Approved)
                    ->setParameter('archive', FALSE);

        $patient = $qb->getQuery()->getScalarResult();
       
        return $patientCount = sizeof($patient);
    }
    
    
    public function pendingHospitalCount()
    {
          $qb =  $this->entityManagerService
                    ->getRepository(Hospital::class)
                    ->createQueryBuilder('h')
                    ->where('h.status = :status and h.isArchived = :archive')
                    ->setParameter('status', Status::Pending)
                    ->setParameter('archive', FALSE);

        $hospital = $qb->getQuery()->getScalarResult();
        
        return $hospitalCount = sizeof($hospital);
    }
    
    public function pendingDoctorCount()
    {
          $qb =  $this->entityManagerService
                    ->getRepository(Doctor::class)
                    ->createQueryBuilder('d')
                    ->where('d.status = :status and d.isArchived = :archive')
                    ->setParameter('status', Status::Pending)
                    ->setParameter('archive', FALSE);

        $doctor = $qb->getQuery()->getScalarResult();
        
        return $doctorCount = sizeof($doctor);
    }
    
    public function pendingPatientCount()
    {
          $qb =  $this->entityManagerService
                    ->getRepository(Patient::class)
                    ->createQueryBuilder('p')
                    ->where('p.status = :status and p.isArchived = :archive')
                    ->setParameter('status', Status::Pending)
                    ->setParameter('archive', FALSE);

        $patient = $qb->getQuery()->getScalarResult();
       
        return $patientCount = sizeof($patient);
    }
     public function getPaginatedAdmin(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManagerService
            ->getRepository(Admin::class)
            ->createQueryBuilder('a')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('a.isHeadAdmin = :isHeadAdmin')
            ->setParameter(':isHeadAdmin', FALSE);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contactNumber', 'address', 
            'approvedAt', 'profilePicture', 'createdAt', 'storageFilename'
            ]) ? $params->orderBy : 'createdAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('a.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('a.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function setHeadAdmin(Admin $admin): AdminInterface
    {
        $admin->setIsHeadAdmin(TRUE);
        return $admin;
    }
    public function setAssistantAdmin(): AdminInterface
    {
        $adminId = $this->session->get('admin');
        $admin = $this->adminProviderService->getById($adminId);

        $admin->setIsHeadAdmin(FALSE);

        return $admin;
    }
    
    
}
