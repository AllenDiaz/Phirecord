<?php

declare(strict_types = 1);

namespace App\Services;

use App\Enum\Status;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Contracts\SessionInterface;
use App\DataObjects\DataTableQueryParams;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;

class HospitalService
{
public function __construct(
    private readonly EntityManagerServiceInterface $entityManagerService,
    private readonly SessionInterface $session
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
    
    public function getPaginatedPendingHospital (DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManagerService
            ->getRepository(Hospital::class)
            ->createQueryBuilder('h')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('h.status = :status and h.isArchived = :archive')
            ->setParameter(':status', '0' )
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

    public function getPaginatedArchiveApproveHospital(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManagerService
            ->getRepository(Hospital::class)
            ->createQueryBuilder('h')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('h.status = :status and h.isArchived = :archive')
            ->setParameter(':status', Status::Approved )
            ->setParameter(':archive', TRUE);

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

    public function getPaginatedArchiveDeclinedHospital(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManagerService
            ->getRepository(Hospital::class)
            ->createQueryBuilder('h')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('h.status = :status and h.isArchived = :archive')
            ->setParameter(':status', '0')
            ->setParameter(':archive', TRUE);

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


    public function getById(int $id): ?Hospital
    {
        return $this->entityManagerService->find(Hospital::class, $id);
    }

    public function update(Hospital $hospital, string $name): Hospital
    {
        $hospital->setName($name);

        return $category;
    }

    public function getHospitalNames(): array
    {
        return $this->entityManagerService
            ->getRepository(Hospital::class)->createQueryBuilder('h')
            ->select('h.id', 'h.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function getReferralHospital(): array
    {   
        $hospitalId = $this->session->get('hospital');

        return $this->entityManagerService
            ->getRepository(Hospital::class)->createQueryBuilder('h')
            ->select('h.id', 'h.name')
            ->where('h.id != :hospitalId')
            ->setParameter('hospitalId', $hospitalId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findByName(string $name): ?Hospital
    {
        return $this->entityManagerService->getRepository(Hospital::class)->findBy(['name' => $name])[0] ?? null;
    }

    public function getAllKeyedByName(): array
    {
        $hospitals  = $this->entityManagerService->getRepository(Hospital::class)->findAll();
        $categoryMap = [];

        foreach ($hospitals as $hospital) {
            $categoryMap[strtolower($hospital->getName())] = $hospital;
        }

        return $categoryMap;
    }
    public function activateStatus(Hospital $hospital): Hospital
    {
        $hospital->setStatus('1');
        $hospital->setApprovedAt(new \DateTime);
        return $hospital;
    }
    public function toArchive(Hospital $hospital): Hospital
    {
        $hospital->setIsArchived(true);

         $hospital->getDoctors()->map(fn(Doctor $doctor) => [
                    $doctor->setIsArchived(true),
         ]);

         $hospital->getPatients()->map(fn(Patient $patient) => [
                    $patient->setIsArchived(true),
         ]);

        return $hospital;
    }

    public function pendingToArchive(Hospital $hospital): Hospital
    {
        $hospital->setStatus('0');
        $hospital->setIsArchived(true);
        return $hospital;
    }

    public function archiveToPending(Hospital $hospital): Hospital
    {
        $hospital->setStatus('0');
        $hospital->setIsArchived(false);
        return $hospital;
    }

    public function archiveToApproved(Hospital $hospital): Hospital
    {
        $hospital->setIsArchived(false);
        $hospital->getDoctors()->map(fn(Doctor $doctor) => [
                    $doctor->setIsArchived(false),
         ]);

         $hospital->getPatients()->map(fn(Patient $patient) => [
                    $patient->setIsArchived(false),
         ]);
        return $hospital;
    }

    public function approvedDoctorCount()
    {
        $hospitalId = $this->session->get('hospital');

          $qb =  $this->entityManagerService
                    ->getRepository(Doctor::class)
                    ->createQueryBuilder('d')
                    ->where('d.status = :status and d.isArchived = :archive and d.hospital = :hospital')
                    ->setParameter('status', Status::Approved)
                    ->setParameter('archive', FALSE)
                    ->setParameter('hospital', $hospitalId);

        $doctor = $qb->getQuery()->getScalarResult();
        
        return $doctorCount = sizeof($doctor);
    }
    
    public function approvedPatientCount()
    {
        $hospitalId = $this->session->get('hospital');
          $qb =  $this->entityManagerService
                    ->getRepository(Patient::class)
                    ->createQueryBuilder('p')
                    ->where('p.status = :status and p.isArchived = :archive and p.hospital = :hospital')
                    ->setParameter('status', Status::Approved)
                    ->setParameter('archive', FALSE)
                    ->setParameter('hospital', $hospitalId);

        $patient = $qb->getQuery()->getScalarResult();
       
        return $patientCount = sizeof($patient);
    }

     public function pendingDoctorCount()
    {
        $hospitalId = $this->session->get('hospital');
          $qb =  $this->entityManagerService
                    ->getRepository(Doctor::class)
                    ->createQueryBuilder('d')
                    ->where('d.status = :status and d.isArchived = :archive and d.hospital = :hospital')
                    ->setParameter('status', '0')
                    ->setParameter('archive', FALSE)
                    ->setParameter('hospital', $hospitalId);
        $doctor = $qb->getQuery()->getScalarResult();
        
        return $doctorCount = sizeof($doctor);
    }
    
    public function pendingPatientCount()
    {
          $hospitalId = $this->session->get('hospital');
          $qb =  $this->entityManagerService
                    ->getRepository(Patient::class)
                    ->createQueryBuilder('p')
                    ->where('p.status = :status and p.isArchived = :archive and p.hospital = :hospital')
                    ->setParameter('status', '0')
                    ->setParameter('archive', FALSE)
                    ->setParameter('hospital', $hospitalId);

        $patient = $qb->getQuery()->getScalarResult();
       
        return $patientCount = sizeof($patient);
    }
    
  

    
}
