<?php
declare(strict_types = 1);

namespace App\Services;

use App\Entity\Referral;
use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\DataObjects\ReferPatientData;
use App\DataObjects\DataTableQueryParams;
use App\Services\HospitalProviderService;
use App\DataObjects\MedicalCertificateData;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Contracts\EntityManagerServiceInterface;

class ReferPatientService
{
    public function __construct(
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalProviderService $hospitalProvider

    )
    {
    }

    public function submitForm(ReferPatientData $data): void
    {
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
        }


        $hospitalId = $this->session->get('hospital');

        $hospital = $this->hospitalProvider->getById($hospitalId);

        $referral = new Referral();


        $referral->setHospital($hospital);
        $referral->setPatient($data->patient);
        $referral->setToHospital($data->referHospital);
        $referral->setReferralCode($code);
    
        $this->entityManagerService->sync($referral);

    }

    public function getPaginatedPending(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Referral::class)
            ->createQueryBuilder('r')
            ->select('r', 'h', 'p')
            ->leftJoin('r.hospital', 'h')
            ->leftJoin('r.patient', 'p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('r.toHospital = :hospital and r.isAccepted = :isAccepted')
            ->setParameter('hospital', $hospitalId)
            ->setParameter('isAccepted', FALSE );

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'referralCode', 'createdAt'

            ]) ? $params->orderBy : 'createdAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('r.referralCode LIKE :referralCode')->setParameter(
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
        else {
            $query->orderBy('r.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

    public function getPaginatedAccepted(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Referral::class)
            ->createQueryBuilder('r')
            ->select('r', 'h', 'p')
            ->leftJoin('r.hospital', 'h')
            ->leftJoin('r.patient', 'p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('r.toHospital = :hospital and r.isAccepted = :isAccepted')
            ->setParameter('hospital', $hospitalId)
            ->setParameter('isAccepted', TRUE );

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'referralCode', 'createdAt'

            ]) ? $params->orderBy : 'createdAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('r.referralCode LIKE :referralCode')->setParameter(
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
        else {
            $query->orderBy('r.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }

    public function approveReferral(Referral $referral): Referral
    {
        $referral->setIsAccepted(TRUE);

        return $referral;
    }
    public function getPaginatedHospitalReffer(DataTableQueryParams $params): Paginator
    {
        $hospitalId = $this->session->get('hospital');

        $query = $this->entityManagerService
            ->getRepository(Referral::class)
            ->createQueryBuilder('r')
            ->select('r', 'h', 'p')
            ->leftJoin('r.hospital', 'h')
            ->leftJoin('r.patient', 'p')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length)
            ->where('r.hospital = :hospital')
            ->setParameter('hospital', $hospitalId);

        $orderBy  = in_array($params->orderBy, [
            'patient', 'hospital', 'referralCode', 'createdAt'

            ]) ? $params->orderBy : 'createdAt';
        $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            $query->andWhere('r.referralCode LIKE :referralCode')->setParameter(
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
        else {
            $query->orderBy('r.' . $orderBy, $orderDir);
        }

        return new Paginator($query);
    }




}