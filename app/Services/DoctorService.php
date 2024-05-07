<?php

declare(strict_types = 1);

namespace App\Services;

use App\Enum\Status;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Contracts\DoctorInterface;
use App\Contracts\SessionInterface;
use App\DataObjects\RegisterDoctorData;
use App\DataObjects\DataTableQueryParams;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\DoctorProviderServiceInterface;

class DoctorService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly DoctorProviderServiceInterface $doctorProviderService,
        ) 
    {
    }

    public function registerDoctor(RegisterDoctorData $data): DoctorInterface 
    {
        $doctor = new Doctor();

        $doctor->setName($data->name);
        $doctor->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));
        $doctor->setBirthdate($data->birthDate);
        $doctor->setAddress($data->address);
        $doctor->setGender($data->sex);
        $doctor->setHospital($data->hospital);
        $doctor->setEmail($data->email);
        $doctor->setContact($data->contactNo);
        $doctor->setIdFilename($data->idFilename);
        $doctor->setStorageIdFilename($data->storageIdFilename);
        $doctor->setEmpFilename($data->empFilename);
        $doctor->setStorageEmpFilename($data->StorageEmpFilename);
        $doctor->setStatus('1');
        $doctor->setApprovedAt( new \DateTime());

        $this->entityManagerService->sync($doctor);

        return $doctor;
    }

    public function getDoctorNames(): array
    {
        $hospitalId = $this->session->get('hospital');

        return $this->entityManagerService
            ->getRepository(Doctor::class)->createQueryBuilder('d')
            ->select('d.id', 'd.name')
            ->where('d.hospital = :hospital')
            ->setParameter('hospital', $hospitalId)
            ->getQuery()
            ->getArrayResult();
    }

     public function getPaginatedDoctor(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Doctor::class)
            ->createQueryBuilder('d')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('d.status = :status and d.isArchived = :archive and d.hospital = :hospital')
            ->setParameter(':status', Status::Approved)
            ->setParameter(':archive', FALSE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt'
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('d.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('d.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

     public function getPaginatedPendingDoctor(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Doctor::class)
            ->createQueryBuilder('d')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('d.status = :status and d.isArchived = :archive and d.hospital = :hospital')
            ->setParameter(':status', '0')
            ->setParameter(':archive', FALSE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt'
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('d.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('d.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function getPaginatedAcceptedArchiveDoctor(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Doctor::class)
            ->createQueryBuilder('d')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('d.status = :status and d.isArchived = :archive and d.hospital = :hospital')
            ->setParameter(':status', Status::Approved)
            ->setParameter(':archive', TRUE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt'
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('d.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('d.' . $orderBy, $orderDir);

        return new Paginator($query);
    }

    public function getPaginatedDeclinedArchiveDoctor(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Doctor::class)
            ->createQueryBuilder('d')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('d.status = :status and d.isArchived = :archive and d.hospital = :hospital')
            ->setParameter(':status', '0')
            ->setParameter(':archive', TRUE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt'
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('d.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }

        $query->orderBy('d.' . $orderBy, $orderDir);

        return new Paginator($query);
    }
    public function toArchive(Doctor $doctor): Doctor
    {
        $doctor->setIsArchived(TRUE);
        return $doctor;
    }
    public function accept(Doctor $doctor): Doctor
    {
        $doctor->setStatus('1');
        $doctor->setApprovedAt( new \DateTime);
        return $doctor;
    }

    public function reject(Doctor $doctor): Doctor
    {
        $doctor->setIsArchived(TRUE);
        return $doctor;
    }
    
    public function recover(Doctor $doctor): Doctor
    {
        $doctor->setIsArchived(FALSE);
        return $doctor;
    }

       public function totalDoctorCount()
    {
        $doctorId = $this->session->get('doctor');
        $doctor = $this->doctorProviderService->getById($doctorId);
        $hospitalId = $doctor->getHospital()->getId();

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
        $doctorId = $this->session->get('doctor');
        $doctor = $this->doctorProviderService->getById($doctorId);
        $hospitalId = $doctor->getHospital()->getId();
        
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

}