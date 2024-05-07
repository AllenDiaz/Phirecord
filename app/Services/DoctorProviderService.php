<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Doctor;
use App\Entity\Hospital;
use App\Contracts\DoctorInterface;
use App\Contracts\HospitalInterface;
use App\DataObjects\RegisterDoctorData;
use App\DataObjects\RegisterHospitalData;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\DoctorProviderServiceInterface;
use App\Contracts\HospitalProviderServiceInterface;


class DoctorProviderService implements DoctorProviderServiceInterface
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {

    }
    
    public function getById(int $doctorId): ?DoctorInterface
    {
        return $this->entityManager->find(Doctor::class, $doctorId);
    }

    public function getByCredentials(array $credentials): ?DoctorInterface
    {
        return $this->entityManager->getRepository(Doctor::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createDoctor(RegisterDoctorData $data): DoctorInterface
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



        $this->entityManager->sync($doctor);

        return $doctor;
    }


}