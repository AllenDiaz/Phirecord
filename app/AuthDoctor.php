<?php

declare(strict_types=1);

namespace App;

use App\Enum\AuthAttemptStatus;
use Doctrine\ORM\EntityManager;
use App\Mail\TwoFactorAuthEmail;
use App\Contracts\DoctorInterface;
use App\Contracts\SessionInterface;
use App\Contracts\AuthDoctorInterface;
use App\DataObjects\RegisterDoctorData;
use App\Services\DoctorLoginCodeService;
use App\Contracts\DoctorProviderServiceInterface;

class AuthDoctor implements AuthDoctorInterface
{
    private ?DoctorInterface $doctor = null; 

    public function __construct(
    private readonly DoctorProviderServiceInterface $doctorProvider, 
    private readonly SessionInterface $session,
    private readonly DoctorLoginCodeService $doctorLoginCodeService,
    private readonly TwoFactorAuthEmail $twoFactorAuthEmail,
    
    )
    {
    }
    public function doctor(): ?DoctorInterface
    {

        if($this->doctor !== null){
            return $this->doctor;
        }

        $doctorId = $this->session->get('doctor');

        if(! $doctorId){
            return null;
        }

        $doctor = $this->doctorProvider->getById($doctorId);

        if(! $doctor) {
            return null;
        }

        $this->doctor = $doctor;

        return $this->doctor;

    }
    
    public function attemptLogin(array $credentials): AuthAttemptStatus
    {
        
       $doctor = $this->doctorProvider->getByCredentials($credentials);

        if (! $doctor || ! $this->checkCredentials($doctor, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }
        // if ($doctor->hasTwoFactorAuthEnabled()) {
        //     $this->startLoginWith2FA($doctor);

        //     return AuthAttemptStatus::TWO_FACTOR_AUTH;
        // }

        $this->logIn($doctor);

        return AuthAttemptStatus::SUCCESS;
    }

    public function checkCredentials(DoctorInterface $doctor, array $credentials): bool
    {
        return password_verify($credentials['password'], $doctor->getPassword());
    }

    public function logout(): void
    {
        $this->session->forget('doctor');
        $this->session->regenerate();

        $this->doctor = null;
    }

    public function register(RegisterDoctorData $data): DoctorInterface
    {
        $doctor = $this->doctorProvider->createDoctor($data);

        $this->logIn($doctor);

        return $doctor;
    }

    public function logIn(DoctorInterface $doctor): void
    {
        $this->session->regenerate();
        $this->session->put('doctor', $doctor->getId());

        $this->doctor = $doctor;
    }
    public function startLoginWith2FA(DoctorInterface $doctor): void
    {
        $this->session->regenerate();
        $this->session->put('2fa', $doctor->getId());

        $this->doctorLoginCodeService->deactivateAllActiveCodes($doctor);

        //sending sms
        $this->twoFactorAuthEmail->sendDoctor($this->doctorLoginCodeService->generate($doctor));
    
    }   
    public function attemptTwoFactorLogin(array $data): bool
    {
        $doctorId = $this->session->get('2fa');

        if (! $doctorId) {
            return false;
        }

        $doctor = $this->doctorProvider->getById($doctorId);

        if (! $doctor || $doctor->getEmail() !== $data['email']) {
            return false;
        }
        if (! $this->doctorLoginCodeService->verify($doctor, $data['code'])) {
            return false;
        }

        $this->session->forget('2fa');

        $this->login($doctor);

        return true;
    }

}