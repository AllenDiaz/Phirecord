<?php

declare(strict_types=1);

namespace App;

use App\Enum\AuthAttemptStatus;
use Doctrine\ORM\EntityManager;
use App\Mail\TwoFactorAuthEmail;
use App\Contracts\AdminInterface;
use App\Contracts\SessionInterface;
use App\Contracts\HospitalInterface;
use App\DataObjects\RegisterAdminData;
use App\Contracts\AuthHospitalInterface;
use App\DataObjects\RegisterHospitalData;
use App\Services\HospitalLoginCodeService;
use App\DataObjects\RegisterAdminHospitalData;
use App\Contracts\AdminProviderServiceInterface;
use App\Contracts\HospitalProviderServiceInterface;

class AuthHospital implements AuthHospitalInterface
{
    private ?HospitalInterface $hospital = null; 

    public function __construct(
        private readonly HospitalProviderServiceInterface $hospitalProvider, 
        private readonly SessionInterface $session,
        private readonly TwoFactorAuthEmail $twoFactorAuthEmail,
        private readonly HospitalLoginCodeService $hospitalLoginCodeService,
        )
    {
    }
    public function hospital(): ?HospitalInterface
    {

        if($this->hospital !== null){
            return $this->hospital;
        }

        $hospitalId = $this->session->get('hospital');

        if(! $hospitalId){
            return null;
        }

        $hospital = $this->hospitalProvider->getById($hospitalId);

        if(! $hospital) {
            return null;
        }

        $this->hospital = $hospital;

        return $this->hospital;

    }
    
    public function attemptLogin(array $credentials): AuthAttemptStatus
    {
        
       $hospital = $this->hospitalProvider->getByCredentials($credentials);

        if (! $hospital || ! $this->checkCredentials($hospital, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }

        // if ($hospital->hasTwoFactorAuthEnabled()) {
        //     $this->startLoginWith2FA($hospital);

        //     return AuthAttemptStatus::TWO_FACTOR_AUTH;
        // }

        $this->logIn($hospital);

        return AuthAttemptStatus::SUCCESS;
    }

    public function checkCredentials(HospitalInterface $hospital, array $credentials): bool
    {
        return password_verify($credentials['password'], $hospital->getPassword());
    }

    public function logout(): void
    {
        $this->session->forget('hospital');
        $this->session->regenerate();

        $this->hospital = null;
    }

    public function register(RegisterHospitalData $data): HospitalInterface
    {
        $hospital = $this->hospitalProvider->createHospital($data);

        $this->logIn($hospital);

        return $hospital;
    }

    public function logIn(HospitalInterface $hospital): void
    {
        $this->session->regenerate();
        $this->session->put('hospital', $hospital->getId());

        $this->hospital = $hospital;
    }
    public function startLoginWith2FA(HospitalInterface $hospital): void
    {
        $this->session->regenerate();
        $this->session->put('2fa', $hospital->getId());

        $this->hospitalLoginCodeService->deactivateAllActiveCodes($hospital);

        //sending sms
        $this->twoFactorAuthEmail->sendHospital($this->hospitalLoginCodeService->generate($hospital));
    
    }   
    public function attemptTwoFactorLogin(array $data): bool
    {
        $hospitalId = $this->session->get('2fa');

        if (! $hospitalId) {
            return false;
        }

        $hospital = $this->hospitalProvider->getById($hospitalId);

        if (! $hospital || $hospital->getEmail() !== $data['email']) {
            return false;
        }
        if (! $this->hospitalLoginCodeService->verify($hospital, $data['code'])) {
            return false;
        }

        $this->session->forget('2fa');

        $this->login($hospital);

        return true;
    }


}