<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Admin;
use App\Entity\Hospital;
use App\Contracts\AdminInterface;
use App\DataObjects\RegisterAdminData;
use App\DataObjects\RegisterAdminHospitalData;
use App\Contracts\AdminProviderServiceInterface;
use App\Contracts\EntityManagerServiceInterface;


class AdminProviderService implements AdminProviderServiceInterface
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {

    }
    
    public function getById(int $adminId): ?AdminInterface
    {
        return $this->entityManager->find(Admin::class, $adminId);
    }

    public function getByCredentials(array $credentials): ?AdminInterface
    {
        return $this->entityManager->getRepository(Admin::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createAdmin(RegisterAdminData $data): AdminInterface
    {
        $admin = new Admin();
        
        $admin->setName($data->name);
        $admin->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));
        $admin->setEmail($data->email);
        $admin->setBirthdate($data->birthdate);
        $admin->setGender($data->gender);
        $admin->setContactNumber($data->contact);
        $admin->setAddress($data->address);
        $admin->setFilename($data->filename);
        $admin->setStorageFilename($data->storageFilename);
        $admin->setFilename($data->filename);
        $admin->setStorageFilename($data->storageFilename);


        $this->entityManager->sync($admin);

        return $admin;
    }

    public function createAdminHospital(RegisterAdminHospitalData $data): void
    {
        $hospital = new Hospital();
        
        $hospital->setName($data->name);
        $hospital->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));
        $hospital->setEmail($data->email);
        $hospital->setContactNumber($data->contactNo);
        $hospital->setAddress($data->address);
        $hospital->setStatus('1');
        $hospital->setFilename($data->filenameProof);
        $hospital->setStorageFilename($data->storageFilenameProof);
        $hospital->setHospitalFilename($data->filenameProfile);
        $hospital->setHospitalStorageFilename($data->storageFilenameProfile);
        $hospital->setApprovedAt(new \DateTime);


        $this->entityManager->sync($hospital);
    }


}