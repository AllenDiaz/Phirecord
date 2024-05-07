<?php
declare(strict_types = 1);

namespace App\Services;

use App\Entity\Prescription;
use App\Entity\AdmissionForm;
use App\Entity\PrenatalCheckup;
use App\Contracts\SessionInterface;
use App\DataObjects\CheckupFormData;
use App\DataObjects\DataTableQueryParams;
use App\Services\HospitalProviderService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\PatientProviderServiceInterface;

class CheckupFormService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalProviderService $hospitalProvider,
        private readonly PatientProviderServiceInterface $patientProviderService

    )
    {
    }

    public function submitForm(CheckupFormData $data): void
    {
        $hospitalId = $this->session->get('hospital');

        $hospital = $this->hospitalProvider->getById($hospitalId);

        $checkupForm = new PrenatalCheckup();

        $checkupForm->setConfineDateEstimated($data->confineDate);
        $checkupForm->setCheckupDate($data->checkupDate);
        $checkupForm->setHospital($hospital);
        $checkupForm->setPatient($data->patient);
        $checkupForm->setDoctor($data->doctor);
        $checkupForm->setFamilyMember($data->familyMember);
        $checkupForm->setLastMenstrualDate($data->menstrualDate);
        $checkupForm->setFetalHeartTones($data->fetalHeartTones);
        $checkupForm->setGravida($data->gravida);
        $checkupForm->setPara($data->para);
        $checkupForm->setLabaratory($data->labaratory);
        $checkupForm->setUrinalysis($data->urinalysis);
        $checkupForm->setBloodCount($data->bloodCount);
        $checkupForm->setFecalysis($data->fecalysis);

        $this->entityManagerService->sync($checkupForm);

    }

    public function updateForm(PrenatalCheckup $checkupForm, CheckupFormData $data): PrenatalCheckup
    {

        $checkupForm->setConfineDateEstimated($data->confineDate);
        $checkupForm->setCheckupDate($data->checkupDate);
        $checkupForm->setPatient($data->patient);
        $checkupForm->setDoctor($data->doctor);
        $checkupForm->setFamilyMember($data->familyMember);
        $checkupForm->setLastMenstrualDate($data->menstrualDate);
        $checkupForm->setFetalHeartTones($data->fetalHeartTones);
        $checkupForm->setGravida($data->gravida);
        $checkupForm->setPara($data->para);
        $checkupForm->setLabaratory($data->labaratory);
        $checkupForm->setUrinalysis($data->urinalysis);
        $checkupForm->setBloodCount($data->bloodCount);
        $checkupForm->setFecalysis($data->fecalysis);

        return $checkupForm;

    }
     public function getPaginatedCheckup(DataTableQueryParams $params): Paginator
    {
        $patient = $this->session->get('patientSearch');

        $query = $this->entityManagerService
            ->getRepository(PrenatalCheckup::class)
            ->createQueryBuilder('c')
            ->select('c', 'h', 'p', 'd')
            ->leftJoin('c.hospital', 'h')
            ->leftJoin('c.patient', 'p')
            ->leftJoin('c.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('c.patient = :patient')
            ->setParameter('patient', $patient);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'checkupDate'

            ]) ? $params->orderBy : 'checkupDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }
         if ($orderBy === 'patient') {
            $query->orderBy('p.name', $orderDir);
        } 
        elseif ($orderBy === 'hospital') {
            $query->orderBy('h.name', $orderDir);
        } 
        elseif ($orderBy === 'doctor') {
            $query->orderBy('d.name', $orderDir);
        } 
        else {
            $query->orderBy('c.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

     public function getPaginatedDoctorPendingPrescription(DataTableQueryParams $params): Paginator
    {
        $doctorId = $this->session->get('doctor');

        $query = $this->entityManagerService
            ->getRepository(PrenatalCheckup::class)
            ->createQueryBuilder('c')
            ->select('c', 'h', 'p', 'd')
            ->leftJoin('c.hospital', 'h')
            ->leftJoin('c.patient', 'p')
            ->leftJoin('c.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('c.doctor = :doctor and c.isPrescribed = :isPrescribed')
            ->setParameter('doctor', $doctorId)
            ->setParameter('isPrescribed', FALSE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'checkupDate', 'prescription'

            ]) ? $params->orderBy : 'checkupDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }
         if ($orderBy === 'patient') {
            $query->orderBy('p.name', $orderDir);
        } 
        elseif ($orderBy === 'hospital') {
            $query->orderBy('h.name', $orderDir);
        } 
        elseif ($orderBy === 'doctor') {
            $query->orderBy('d.name', $orderDir);
        } 
        else {
            $query->orderBy('c.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }
     public function getPaginatedDoctorPrescription(DataTableQueryParams $params): Paginator
    {
        $doctorId = $this->session->get('doctor');

        $query = $this->entityManagerService
            ->getRepository(PrenatalCheckup::class)
            ->createQueryBuilder('c')
            ->select('c', 'h', 'p', 'd')
            ->leftJoin('c.hospital', 'h')
            ->leftJoin('c.patient', 'p')
            ->leftJoin('c.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('c.doctor = :doctor and c.isPrescribed = :isPrescribed')
            ->setParameter('doctor', $doctorId)
            ->setParameter('isPrescribed', TRUE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'checkupDate'

            ]) ? $params->orderBy : 'checkupDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }
         if ($orderBy === 'patient') {
            $query->orderBy('p.name', $orderDir);
        } 
        elseif ($orderBy === 'hospital') {
            $query->orderBy('h.name', $orderDir);
        } 
        elseif ($orderBy === 'doctor') {
            $query->orderBy('d.name', $orderDir);
        } 
        else {
            $query->orderBy('c.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

     public function getPaginatedPatientCheckup(DataTableQueryParams $params): Paginator
    {
        $patientId = $this->session->get('patient');

        $query = $this->entityManagerService
            ->getRepository(PrenatalCheckup::class)
            ->createQueryBuilder('c')
            ->select('c', 'h', 'p', 'd')
            ->leftJoin('c.hospital', 'h')
            ->leftJoin('c.patient', 'p')
            ->leftJoin('c.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('c.patient = :patient')
            ->setParameter('patient', $patientId);
      

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'checkupDate'

            ]) ? $params->orderBy : 'checkupDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }
         if ($orderBy === 'patient') {
            $query->orderBy('p.name', $orderDir);
        } 
        elseif ($orderBy === 'hospital') {
            $query->orderBy('h.name', $orderDir);
        } 
        elseif ($orderBy === 'doctor') {
            $query->orderBy('d.name', $orderDir);
        } 
        else {
            $query->orderBy('c.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

     public function getPaginatedRequestPatientCheckup(DataTableQueryParams $params): Paginator
    {
        $patientId = $this->session->get('patient');

        $query = $this->entityManagerService
            ->getRepository(PrenatalCheckup::class)
            ->createQueryBuilder('c')
            ->select('c', 'h', 'p', 'd', 'r')
            ->leftJoin('c.hospital', 'h')
            ->leftJoin('c.patient', 'p')
            ->leftJoin('c.doctor', 'd')
            ->leftJoin('c.requestCheckup', 'r')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('c.patient = :patient and c.requested = :requested')
            ->setParameter('patient', $patientId)
            ->setParameter('requested', TRUE);
      

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'checkupDate', 'referenceCode'

            ]) ? $params->orderBy : 'checkupDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('p.name LIKE :name')->setParameter(
                'name',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }
         if ($orderBy === 'patient') {
            $query->orderBy('p.name', $orderDir);
        } 
        elseif ($orderBy === 'hospital') {
            $query->orderBy('h.name', $orderDir);
        } 
        elseif ($orderBy === 'doctor') {
            $query->orderBy('d.name', $orderDir);
        } 
        elseif ($orderBy === 'referenceCode') {
            $query->orderBy('r.checkupCode', $orderDir);
        } 
        else {
            $query->orderBy('c.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }
     public function getPaginatedRequestHospitalCheckup(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(PrenatalCheckup::class)
            ->createQueryBuilder('c')
            ->select('c', 'h', 'p', 'd', 'r')
            ->leftJoin('c.hospital', 'h')
            ->leftJoin('c.patient', 'p')
            ->leftJoin('c.doctor', 'd')
            ->leftJoin('c.requestCheckup', 'r')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('c.hospital = :hospital and c.requested = :requested')
            ->setParameter('hospital', $hospitalId)
            ->setParameter('requested', TRUE);
      

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'checkupDate', 'referenceCode'

            ]) ? $params->orderBy : 'checkupDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('r.checkupCode LIKE :referenceCode')->setParameter(
                'referenceCode',
                '%' . addcslashes($params->searchTerm, '%_') . '%'
            );
        }
         if ($orderBy === 'patient') {
            $query->orderBy('p.name', $orderDir);
        } 
        elseif ($orderBy === 'hospital') {
            $query->orderBy('h.name', $orderDir);
        } 
        elseif ($orderBy === 'doctor') {
            $query->orderBy('d.name', $orderDir);
        } 
        elseif ($orderBy === 'referenceCode') {
            $query->orderBy('r.checkupCode', $orderDir);
        } 
        else {
            $query->orderBy('c.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }
    public function create(PrenatalCheckup $prenatalCheckup, $storageFilename): Prescription
    {
        $prescription = new Prescription();
        $prescription->setPrenatalCheckup($prenatalCheckup);
        $prescription->setStorageFilename($storageFilename);

        $this->entityManagerService->sync($prenatalCheckup);

        return $prescription;

    }
    public function totalPatientForm()
    {
        $patientId = $this->session->get('patient');

          $qb =  $this->entityManagerService
                    ->getRepository(PrenatalCheckup::class)
                    ->createQueryBuilder('p')
                    ->where('p.patient = :patient')
                    ->setParameter('patient', $patientId);

        $checkupRecord = $qb->getQuery()->getScalarResult();
        
        return $checkupCount = sizeof($checkupRecord);
    }
}