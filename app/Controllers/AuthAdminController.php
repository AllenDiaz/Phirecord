<?php

declare(strict_types = 1);

namespace App\Controllers;

use Slim\Views\Twig;
use App\ResponseFormatter;
use App\Enum\AuthAttemptStatus;
use League\Flysystem\Filesystem;
use App\Contracts\SessionInterface;
use App\Contracts\AuthAdminInterface;
use App\DataObjects\RegisterAdminData;
use App\Exception\ValidationException;
use Psr\Http\Message\UploadedFileInterface;
use App\RequestValidators\UploadGovIdValidator;
use App\Contracts\AdminProviderServiceInterface;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\AdminLoginRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\RegisterAdminRequestValidator;
use App\RequestValidators\TwoFactorLoginRequestValidator;

class AuthAdminController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly AuthAdminInterface $auth,
        private readonly AdminProviderServiceInterface $adminProvider,
        private readonly SessionInterface $session,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly ResponseFormatter $responseFormatter

    )
    {
    }

    public function loginView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/admin_login.twig');
    }

    public function registerView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/admin_register.twig', );
    } 
    public function register(Request $request, Response $response): Response
    {
        // 1: Validate the files 
        /** @var UploadedFileInterface $fileData */
        $fileData = $this->requestValidatorFactory->make(UploadGovIdValidator::class)->validate(
            $request->getUploadedFiles()
        )['govId'];

        $data = $this->requestValidatorFactory->make(RegisterAdminRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        
            
        // 2: Store the files
        $filename = $fileData->getClientFilename();

        $extension = pathinfo($fileData->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));

        $randomFilename = sprintf('%s.%0.8s', $basename, $extension);

        $this->filesystem->write('img/admin/' . $randomFilename, $fileData->getStream()->getContents());

        $birthdate = new \DateTime($data['birthdate']);

        $this->auth->register(new RegisterAdminData(
            $data['name'], $data['password'], $birthdate, $data['gender'],
            $data['address'], $data['email'], $data['contact'], $filename,
            $randomFilename
            
        ));
        // 3: heading of the file 
        return $response->withHeader('Location', '/admin/head')->withStatus(302);
    }
    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(AdminLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        
        $status = $this->auth->attemptLogin($data);
        
        if ($status === AuthAttemptStatus::FAILED) {
            throw new ValidationException(['password' => ['You have entered an invalid username or password']]);
        }
        if ($status === AuthAttemptStatus::TWO_FACTOR_AUTH) {
            return $this->responseFormatter->asJson($response, ['two_factor' => true]);
        }

        return $this->responseFormatter->asJson($response, []);
    }
    public function logout(Request $request, Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }
    public function twoFactorLogin(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(TwoFactorLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if (! $this->auth->attemptTwoFactorLogin($data)) {
            throw new ValidationException(['code' => ['Invalid Code']]);
        }

        return $response;
    }
    
}