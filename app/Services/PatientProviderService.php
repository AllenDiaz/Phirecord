<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Patient;
use App\Contracts\PatientInterface;
use App\Services\PatientProviderService;
use App\DataObjects\RegisterPatientData;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\PatientProviderServiceInterface;


class PatientProviderService implements PatientProviderServiceInterface
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManager)
    {

    }
    
    public function getById(int $patientId): ?PatientInterface
    {
        return $this->entityManager->find(Patient::class, $patientId);
    }

    public function getByCredentials(array $credentials): ?PatientInterface
    {
        return $this->entityManager->getRepository(Patient::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createPatient(RegisterPatientData $data): PatientInterface
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



        $this->entityManager->sync($patient);

        return $patient;
    }


}