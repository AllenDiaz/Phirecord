<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Hospital;
use App\Contracts\HospitalInterface;
use App\DataObjects\RegisterHospitalData;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\HospitalProviderServiceInterface;


class HospitalProviderService implements HospitalProviderServiceInterface
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {

    }
    
    public function getById(int $hospitalId): ?HospitalInterface
    {
        return $this->entityManager->find(Hospital::class, $hospitalId);
    }

    public function getByCredentials(array $credentials): ?HospitalInterface
    {
        return $this->entityManager->getRepository(Hospital::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createHospital(RegisterHospitalData $data): HospitalInterface
    {
        $hospital = new Hospital();
        
        $hospital->setName($data->name);
        $hospital->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));
        $hospital->setEmail($data->email);
        $hospital->setContactNumber($data->contactNo);
        $hospital->setAddress($data->address);
        $hospital->setStatus('2');
        $hospital->setFilename($data->filenameProof);
        $hospital->setStorageFilename($data->storageFilenameProof);
        $hospital->setHospitalFilename($data->filenameProfile);
        $hospital->setHospitalStorageFilename($data->storageFilenameProfile);
        $hospital->setCreatedAt(new \DateTime);


        $this->entityManager->sync($hospital);

        return $hospital;
    }


}