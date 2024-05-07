<?php

declare(strict_types = 1);

namespace App\Controllers;

use Slim\Views\Twig;
use App\ResponseFormatter;

use App\Enum\AuthAttemptStatus;
use League\Flysystem\Filesystem;
use App\Services\UploadFileService;
use App\Exception\ValidationException;
use App\Contracts\AuthHospitalInterface;
use App\DataObjects\RegisterHospitalData;
use Psr\Http\Message\UploadedFileInterface;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\UploadHospitalProofValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\HospitalLoginRequestValidator;
use App\RequestValidators\TwoFactorLoginRequestValidator;
use App\RequestValidators\RegisterHospitalRequestValidator;

class AuthHospitalController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly AuthHospitalInterface $auth,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly ResponseFormatter $responseFormatter,
        private readonly UploadFileService $uploadFile,

    )
    {
    }

    public function loginView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/hospital_login.twig');
    }

    public function registerView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/hospital_register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        // 1  Validated the data
        $data = $this->requestValidator->make(RegisterHospitalRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        /** @var UploadedFileInterface $fileData */
        // For Proof
        $fileproofData = $this->requestValidator->make(UploadHospitalProofValidator::class)->validate( $request->getUploadedFiles(), 'govProof');
        
        $hospitalProofPhoto = $this->uploadFile->upload($fileproofData,'govProof','img/hospital/');
        
        $filenameProof = $hospitalProofPhoto['filename'];
        $storageNameProof = $hospitalProofPhoto['pathName'];

        // For Hospital Profile Picture 
        $fileProfile = $this->requestValidator->make(UploadHospitalProofValidator::class)->validate($request->getUploadedFiles(), 'hospitalPhoto');
        
        $hospitalProfilePhoto = $this->uploadFile->upload($fileProfile, 'hospitalPhoto', 'img/hospital/');
        
        $filenameProfile = $hospitalProfilePhoto['filename'];
        $storageNameProfile = $hospitalProfilePhoto['pathName'];
        

        $this->auth->register(new RegisterHospitalData(
            $data['name'], $data['address'], $data['email'], $data['contactNo'],
            $data['password'], $filenameProof,
            $storageNameProof, $filenameProfile, $storageNameProfile
            
        ));

        return $response->withHeader('Location', '/hospital')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidator->make(HospitalLoginRequestValidator::class)->validate(
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

        return $response->withHeader('Location', '/hospital/login')->withStatus(302);
    }

    public function twoFactorLogin(Request $request, Response $response): Response
    {
        $data = $this->requestValidator->make(TwoFactorLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if (! $this->auth->attemptTwoFactorLogin($data)) {
            throw new ValidationException(['code' => ['Invalid Code']]);
        }

        return $response;
    }

    
}

