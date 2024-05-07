<?php

declare(strict_types=1);

namespace App;

use App\Entity\Admin;
use App\Enum\AuthAttemptStatus;
use Doctrine\ORM\EntityManager;
use App\Mail\TwoFactorAuthEmail;
use App\Contracts\AdminInterface;
use App\Contracts\SessionInterface;
use App\Contracts\AuthAdminInterface;
use App\DataObjects\RegisterAdminData;
use App\Services\AdminLoginCodeService;
use App\DataObjects\RegisterAdminHospitalData;
use App\Contracts\AdminProviderServiceInterface;

class AuthAdmin implements AuthAdminInterface
{
    private ?AdminInterface $admin = null; 

    public function __construct(
        private readonly AdminProviderServiceInterface $adminProvider, 
        private readonly SessionInterface $session,
        private readonly AdminLoginCodeService $adminLoginCodeService,
        private readonly TwoFactorAuthEmail $twoFactorAuthEmail,

        
        )
    {
    }
    public function admin(): ?AdminInterface
    {
        if($this->admin !== null){
            return $this->admin;
        }
        
        $adminId = $this->session->get('admin');

        if(! $adminId){
            return null;
        }

        $admin = $this->adminProvider->getById($adminId);

        if(! $admin) {
            return null;
        }

        $this->admin = $admin;

        return $this->admin;

    }
    
    public function attemptLogin(array $credentials): AuthAttemptStatus
    {
        
       $admin = $this->adminProvider->getByCredentials($credentials);

        if (! $admin || ! $this->checkCredentials($admin, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }
        // if ($admin->hasTwoFactorAuthEnabled()) {
        //     $this->startLoginWith2FA($admin);

        //     return AuthAttemptStatus::TWO_FACTOR_AUTH;
        // }

        $this->logIn($admin);

        return AuthAttemptStatus::SUCCESS;
    }

    public function checkCredentials(AdminInterface $admin, array $credentials): bool
    {
        return password_verify($credentials['password'], $admin->getPassword());
    }

    public function logout(): void
    {
        $this->session->forget('admin');
        $this->session->regenerate();

        $this->admin = null;
    }
    public function register(RegisterAdminData $data): AdminInterface
    {
        $admin = $this->adminProvider->createAdmin($data);

        $this->logIn($admin);

        return $admin;
    }

    public function registerAdmin(RegisterAdminData $data): AdminInterface
    {
        $admin = $this->adminProvider->createAdmin($data);

        return $admin;
    }
    public function registerHospital(RegisterAdminHospitalData $data): void
    {
        $hospital = $this->adminProvider->createAdminHospital($data);
    }

    public function logIn(AdminInterface $admin): void
    {
        $this->session->regenerate();
        $this->session->put('admin', $admin->getId());

        $this->admin = $admin;
    }
    public function startLoginWith2FA(AdminInterface $admin): void
    {
        $this->session->regenerate();
        $this->session->put('2fa', $admin->getId());

        $this->adminLoginCodeService->deactivateAllActiveCodes($admin);

        //sending sms
        $this->twoFactorAuthEmail->send($this->adminLoginCodeService->generate($admin));
    
    }   
    public function attemptTwoFactorLogin(array $data): bool
    {
        $adminId = $this->session->get('2fa');

        if (! $adminId) {
            return false;
        }

        $admin = $this->adminProvider->getById($adminId);

        if (! $admin || $admin->getEmail() !== $data['email']) {
            return false;
        }
        if (! $this->adminLoginCodeService->verify($admin, $data['code'])) {
            return false;
        }

        $this->session->forget('2fa');

        $this->login($admin);

        return true;
    }


}