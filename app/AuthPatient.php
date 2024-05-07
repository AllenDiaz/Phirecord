<?php

declare(strict_types=1);

namespace App;

use App\Enum\AuthAttemptStatus;
use Doctrine\ORM\EntityManager;
use App\Mail\TwoFactorAuthEmail;
use App\Contracts\DoctorInterface;
use App\Contracts\PatientInterface;
use App\Contracts\SessionInterface;
use App\Contracts\AuthPatientInterface;
use App\DataObjects\RegisterPatientData;
use App\Services\PatientLoginCodeService;
use App\Contracts\PatientProviderServiceInterface;

class AuthPatient implements AuthPatientInterface
{
    private ?PatientInterface $patient = null; 

    public function __construct(
        private readonly PatientProviderServiceInterface $patientProviderService, 
        private readonly SessionInterface $session,
        private readonly PatientLoginCodeService $patientLoginCodeService,
        private readonly TwoFactorAuthEmail $twoFactorAuthEmail,
        )
    {
    }
    public function patient(): ?PatientInterface
    {

        if($this->patient !== null){
            return $this->patient;
        }

        $patientId = $this->session->get('patient');

        if(! $patientId){
            return null;
        }

        $patient = $this->patientProviderService->getById($patientId);

        if(! $patient) {
            return null;
        }

        $this->patient = $patient;

        return $this->patient;

    }
    
    public function attemptLogin(array $credentials): AuthAttemptStatus
    {
        
       $patient = $this->patientProviderService->getByCredentials($credentials);

        if (! $patient || ! $this->checkCredentials($patient, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }
        // if ($patient->hasTwoFactorAuthEnabled()) {
        //     $this->startLoginWith2FA($patient);

        //     return AuthAttemptStatus::TWO_FACTOR_AUTH;
        // }

        $this->logIn($patient);

        return AuthAttemptStatus::SUCCESS;
    }

    public function checkCredentials(PatientInterface $patient, array $credentials): bool
    {
        return password_verify($credentials['password'], $patient->getPassword());
    }

    public function logout(): void
    {
        $this->session->forget('patient');
        $this->session->regenerate();

        $this->patient = null;
    }

    public function register(RegisterPatientData $data): PatientInterface
    {
        $patient = $this->patientProviderService->createPatient($data);

        $this->logIn($patient);

        return $patient;
    }

    public function logIn(PatientInterface $patient): void
    {
        $this->session->regenerate();
        $this->session->put('patient', $patient->getId());

        $this->patient = $patient;
    }
    public function startLoginWith2FA(PatientInterface $patient): void
    {
        $this->session->regenerate();
        $this->session->put('2fa', $patient->getId());

        $this->patientLoginCodeService->deactivateAllActiveCodes($patient);

        //sending email
        $this->twoFactorAuthEmail->sendPatient($this->patientLoginCodeService->generate($patient));
    
    }   
    public function attemptTwoFactorLogin(array $data): bool
    {
        $patientId = $this->session->get('2fa');

        if (! $patientId) {
            return false;
        }

        $patient = $this->patientProviderService->getById($patientId);

        if (! $patient || $patient->getEmail() !==  $data['email']) {
            return false;
        }
        if (! $this->patientLoginCodeService->verify($patient, $data['code'])) {
            return true;
        }

        $this->session->forget('2fa');

        $this->login($patient);

        return true;
    }

}