<?php

declare(strict_types = 1);

namespace App\Services;

use App\Enum\Status;
use App\Entity\Patient;
use App\Contracts\PatientInterface;
use App\Contracts\SessionInterface;
use App\DataObjects\RegisterPatientData;
use App\DataObjects\DataTableQueryParams;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;

class PatientService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session
        ) 
    {
    }

    public function registerPatient(RegisterPatientData $data): void 
    {
       $patient = new Patient();
        
        $patient->setName($data->name);
        $patient->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));
        $patient->setBirthdate($data->birthDate);
        $patient->setAddress($data->address);
        $patient->setGender($data->sex);
        $patient->setHospital($data->hospital);
        $patient->setEmail($data->email);
        $patient->setContact($data->contactNo);
        $patient->setIdFilename($data->idFilename);
        $patient->setIdStorageFilename($data->idStorageFilename);
        $patient->setPhilhealthNo($data->philhealthNo);
        $patient->setContactGuard($data->contactGuard);
        $patient->setGuardianName($data->guardianName);
        $patient->setStatus('1');
        $patient->setApprovedAt( new \DateTime);



        $this->entityManagerService->sync($patient);

    }

    public function getPaginatedPatient(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Patient::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('p.status = :status and p.isArchived = :archive and p.hospital = :hospital')
            ->setParameter(':status', Status::Approved)
            ->setParameter(':archive', FALSE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'gender', 
            'approvedAt',
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }


        $query->orderBy('p.' . $orderBy, $orderDir);
   


        return new Paginator($query);
    }

    public function getPaginatedPendingPatient(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Patient::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('p.status = :status and p.isArchived = :archive and p.hospital = :hospital')
            ->setParameter(':status', '0')
            ->setParameter(':archive', FALSE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt',
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }


        $query->orderBy('p.' . $orderBy, $orderDir);
   


        return new Paginator($query);
    }
    public function getPaginatedAcceptedArchivePatient(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Patient::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('p.status = :status and p.isArchived = :archive and p.hospital = :hospital')
            ->setParameter(':status', Status::Approved)
            ->setParameter(':archive', TRUE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt',
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }


        $query->orderBy('p.' . $orderBy, $orderDir);
   


        return new Paginator($query);
    }

    public function getPaginatedDeclinedArchivePatient(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Patient::class)
            ->createQueryBuilder('p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('p.status = :status and p.isArchived = :archive and p.hospital = :hospital')
            ->setParameter(':status', '0')
            ->setParameter(':archive', TRUE)
            ->setParameter(':hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'name', 'email', 'contact', 'address', 
            'approvedAt',
            ]) ? $params->orderBy : 'approvedAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }


        $query->orderBy('p.' . $orderBy, $orderDir);
   


        return new Paginator($query);
    }
    public function toArchive(Patient $patient): Patient
    {
        $patient->setIsArchived(true);
        return $patient;
    }
     public function accept(Patient $patient): Patient
    {
        $patient->setStatus('1');
        return $patient;
    }

    public function reject(Patient $patient): Patient
    {
        $patient->setIsArchived(TRUE);
        return $patient;
    }

    public function recover(Patient $patient): Patient
    {
        $patient->setIsArchived(FALSE);
        return $patient;
    }


}