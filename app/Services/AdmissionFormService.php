<?php
declare(strict_types = 1);

namespace App\Services;

use App\Entity\Patient;
use App\Entity\AdmissionForm;
use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\DataObjects\AdmissionFormData;
use App\DataObjects\DataTableQueryParams;
use App\Services\HospitalProviderService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;

class AdmissionFormService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalProviderService $hospitalProvider

    )
    {
    }

    public function submitForm(AdmissionFormData $data): void
    {
        $hospitalId = $this->session->get('hospital');

        $hospital = $this->hospitalProvider->getById($hospitalId);

        $admissionForm = new AdmissionForm();

        $admissionForm->setAdmissionDate($data->admissionDate);
        $admissionForm->setHospital($hospital);
        $admissionForm->setPatient($data->patient);
        $admissionForm->setDoctor($data->doctor);
        $admissionForm->setFamilyMember($data->familyMember);
        $admissionForm->setSymptoms($data->symptoms);
        $admissionForm->setBloodPressure($data->bloodPressure);
        $admissionForm->setTemperature($data->temperature);
        $admissionForm->setWeight($data->weight);
        $admissionForm->setRespiratoryRate($data->respiratoryRate);
        $admissionForm->setPulseRate($data->pulseRate);
        $admissionForm->setOxygenSaturation($data->oxygenSaturation);
        $admissionForm->setDiagnosis($data->diagnosis);

        $this->entityManagerService->sync($admissionForm);

    }

    public function updateForm(AdmissionForm $admissionForm,AdmissionFormData $data): AdmissionForm
    {


        $admissionForm->setAdmissionDate($data->admissionDate);
        $admissionForm->setPatient($data->patient);
        $admissionForm->setDoctor($data->doctor);
        $admissionForm->setFamilyMember($data->familyMember);
        $admissionForm->setSymptoms($data->symptoms);
        $admissionForm->setBloodPressure($data->bloodPressure);
        $admissionForm->setTemperature($data->temperature);
        $admissionForm->setWeight($data->weight);
        $admissionForm->setRespiratoryRate($data->respiratoryRate);
        $admissionForm->setPulseRate($data->pulseRate);
        $admissionForm->setOxygenSaturation($data->oxygenSaturation);
        $admissionForm->setDiagnosis($data->diagnosis);

      return $admissionForm;

    }

    public function getPaginatedAdmission(DataTableQueryParams $params): Paginator
    {
        $patient = $this->session->get('patientSearch');

        $query = $this->entityManagerService
            ->getRepository(AdmissionForm::class)
            ->createQueryBuilder('a')
            ->select('a', 'h', 'p', 'd')
            ->leftJoin('a.hospital', 'h')
            ->leftJoin('a.patient', 'p')
            ->leftJoin('a.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('a.patient = :patient')
            ->setParameter('patient', $patient);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'admissionDate'

            ]) ? $params->orderBy : 'admissionDate';
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
            $query->orderBy('a.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

    public function getPaginatedDoctorAdmission(DataTableQueryParams $params): Paginator
    {
        $doctorId = $this->session->get('doctor');

        $query = $this->entityManagerService
            ->getRepository(AdmissionForm::class)
            ->createQueryBuilder('a')
            ->select('a', 'h', 'p', 'd')
            ->leftJoin('a.hospital', 'h')
            ->leftJoin('a.patient', 'p')
            ->leftJoin('a.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('a.doctor = :doctor and a.prescription = :prescription')
            ->setParameter('doctor', $doctorId)
            ->setParameter('prescription', FALSE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'admissionDate'

            ]) ? $params->orderBy : 'admissionDate';
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
            $query->orderBy('a.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

    public function totalPatientForm()
    {
        $patientId = $this->session->get('patient');

          $qb =  $this->entityManagerService
                    ->getRepository(AdmissionForm::class)
                    ->createQueryBuilder('a')
                    ->where('a.patient = :patient')
                    ->setParameter('patient', $patientId);

        $admissionRecord = $qb->getQuery()->getScalarResult();
        
        return $checkupCount = sizeof($admissionRecord);
    }

    public function getPaginatedPatientAdmission(DataTableQueryParams $params): Paginator
    {
        $patientId = $this->session->get('patient');

        $query = $this->entityManagerService
            ->getRepository(AdmissionForm::class)
            ->createQueryBuilder('a')
            ->select('a', 'h', 'p', 'd')
            ->leftJoin('a.hospital', 'h')
            ->leftJoin('a.patient', 'p')
            ->leftJoin('a.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('a.patient = :patient and a.prescription = :prescription')
            ->setParameter('patient', $patientId)
            ->setParameter('prescription', FALSE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'admissionDate'

            ]) ? $params->orderBy : 'admissionDate';
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
            $query->orderBy('a.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }
    public function getPaginatedPatientRequestAdmission(DataTableQueryParams $params): Paginator
    {
        $patientId = $this->session->get('patient');

        $query = $this->entityManagerService
            ->getRepository(AdmissionForm::class)
            ->createQueryBuilder('a')
            ->select('a', 'h', 'p', 'd', 'r')
            ->leftJoin('a.hospital', 'h')
            ->leftJoin('a.patient', 'p')
            ->leftJoin('a.doctor', 'd')
            ->leftJoin('a.requestAdmission', 'r')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('a.patient = :patient and a.requested = :requested')
            ->setParameter('patient', $patientId)
            ->setParameter('requested', TRUE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'admissionDate', 'referralCode'

            ]) ? $params->orderBy : 'admissionDate';
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
        elseif ($orderBy === 'referralCode') {
            $query->orderBy('r.admissionCode', $orderDir);
        } 
        else {
            $query->orderBy('a.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

    public function getPaginatedHospitalAdmission(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(AdmissionForm::class)
            ->createQueryBuilder('a')
            ->select('a', 'h', 'p', 'd', 'r')
            ->leftJoin('a.hospital', 'h')
            ->leftJoin('a.patient', 'p')
            ->leftJoin('a.doctor', 'd')
            ->leftJoin('a.requestAdmission', 'r')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('a.hospital = :hospital and a.requested = :requested')
            ->setParameter('hospital', $hospitalId)
            ->setParameter('requested', TRUE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'admissionDate', 'referralCode'

            ]) ? $params->orderBy : 'admissionDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('r.admissionCode LIKE :referralCode')->setParameter(
                'referralCode',
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
        elseif ($orderBy === 'referralCode') {
            $query->orderBy('r.admissionCode', $orderDir);
        } 
        else {
            $query->orderBy('a.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }


}