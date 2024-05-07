<?php
declare(strict_types = 1);

namespace App\Services;

use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\DataObjects\DataTableQueryParams;
use App\Services\HospitalProviderService;
use App\DataObjects\MedicalCertificateData;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;

class MedicalCertificateService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalProviderService $hospitalProvider

    )
    {
    }

    public function submitForm(MedicalCertificateData $data): void
    {
        $hospitalId = $this->session->get('hospital');

        $hospital = $this->hospitalProvider->getById($hospitalId);

        $medicalCertificate = new MedicalCertificate();

        $medicalCertificate->setCertificateDate($data->certificateDate);
        $medicalCertificate->setHospital($hospital);
        $medicalCertificate->setPatient($data->patient);
        $medicalCertificate->setDoctor($data->doctor);
        $medicalCertificate->setImpression($data->impression);
        $medicalCertificate->setPurpose($data->purpose);

        $this->entityManagerService->sync($medicalCertificate);

    }
    public function updateForm(MedicalCertificate $medicalCertificate, MedicalCertificateData $data): MedicalCertificate
    {

        $medicalCertificate->setCertificateDate($data->certificateDate);
        $medicalCertificate->setPatient($data->patient);
        $medicalCertificate->setDoctor($data->doctor);
        $medicalCertificate->setImpression($data->impression);
        $medicalCertificate->setPurpose($data->purpose);

        return $medicalCertificate;

    }

        public function getPaginatedMedical(DataTableQueryParams $params): Paginator
    {
        $patient = $this->session->get('patientSearch');

        $query = $this->entityManagerService
            ->getRepository(MedicalCertificate::class)
            ->createQueryBuilder('m')
            ->select('m', 'h', 'p', 'd')
            ->leftJoin('m.hospital', 'h')
            ->leftJoin('m.patient', 'p')
            ->leftJoin('m.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('m.patient = :patient')
            ->setParameter('patient', $patient);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'certificateDate'

            ]) ? $params->orderBy : 'certificateDate';
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
            $query->orderBy('m.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }
    public function getPaginatedPatientMedical(DataTableQueryParams $params): Paginator
    {
       $patientId = $this->session->get('patient');

        $query = $this->entityManagerService
            ->getRepository(MedicalCertificate::class)
            ->createQueryBuilder('m')
            ->select('m', 'h', 'p', 'd')
            ->leftJoin('m.hospital', 'h')
            ->leftJoin('m.patient', 'p')
            ->leftJoin('m.doctor', 'd')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('m.patient = :patient')
            ->setParameter('patient', $patientId);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'certificateDate'

            ]) ? $params->orderBy : 'certificateDate';
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
            $query->orderBy('m.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

    public function getPaginatedPatientRequestMedical(DataTableQueryParams $params): Paginator
    {
       $patientId = $this->session->get('patient');

        $query = $this->entityManagerService
            ->getRepository(MedicalCertificate::class)
            ->createQueryBuilder('m')
            ->select('m', 'h', 'p', 'd', 'r')
            ->leftJoin('m.hospital', 'h')
            ->leftJoin('m.patient', 'p')
            ->leftJoin('m.doctor', 'd')
            ->leftJoin('m.requestMedical', 'r')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('m.patient = :patient and m.requested = :requested')
            ->setParameter('patient', $patientId)
            ->setParameter('requested', TRUE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'requestDate', 'referenceCode'

            ]) ? $params->orderBy : 'requestDate';
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
            $query->orderBy('r.medicalCode', $orderDir);
        }
        elseif ($orderBy === 'requestDate') {
            $query->orderBy('r.createdAt', $orderDir);
        }  
        else {
            $query->orderBy('m.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }
    public function getPaginatHospitalRequestMedical(DataTableQueryParams $params): Paginator
    {
       $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(MedicalCertificate::class)
            ->createQueryBuilder('m')
            ->select('m', 'h', 'p', 'd', 'r')
            ->leftJoin('m.hospital', 'h')
            ->leftJoin('m.patient', 'p')
            ->leftJoin('m.doctor', 'd')
            ->leftJoin('m.requestMedical', 'r')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('m.hospital = :hospital and m.requested = :requested')
            ->setParameter('hospital', $hospitalId)
            ->setParameter('requested', TRUE);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'doctor', 'requestDate', 'referenceCode'

            ]) ? $params->orderBy : 'requestDate';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('r.medicalCode LIKE :referenceCode')->setParameter(
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
            $query->orderBy('r.medicalCode', $orderDir);
        }
        elseif ($orderBy === 'requestDate') {
            $query->orderBy('r.createdAt', $orderDir);
        }  
        else {
            $query->orderBy('m.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

}