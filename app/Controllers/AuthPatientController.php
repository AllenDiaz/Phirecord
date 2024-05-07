<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\AuthDoctor;
use Slim\Views\Twig;
use App\ResponseFormatter;
use App\Enum\AuthAttemptStatus;
use League\Flysystem\Filesystem;
use App\Services\HospitalService;
use App\Services\UploadFileService;
use App\Exception\ValidationException;
use App\Contracts\AuthPatientInterface;
use App\DataObjects\RegisterPatientData;
use Psr\Http\Message\UploadedFileInterface;
use App\RequestValidators\UploadProofValidator;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\PatientLoginRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\RegisterDoctorRequestValidator;
use App\RequestValidators\TwoFactorLoginRequestValidator;
use App\RequestValidators\RegisterPatientRequestValidator;

class AuthPatientController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly HospitalService $hospitalService,
        private readonly UploadFileService $uploadFile,
        private readonly AuthPatientInterface $auth,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly ResponseFormatter $responseFormatter
    )
    {
    }

    public function loginView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/patient_login.twig');
    }

    public function registerView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/patient_register.twig',  ['hospitals' => $this->hospitalService->getHospitalNames()]);
    }
    
    public function register(Request $request, Response $response): Response
    {
        // 1  Validated the data
        $data = $this->requestValidator->make(RegisterPatientRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        /** @var UploadedFileInterface $fileData */
        // For Gov ID
        $fileIdData = $this->requestValidator->make(UploadProofValidator::class)->validate( $request->getUploadedFiles(), 'govId');
        
        $patientIdPhoto = $this->uploadFile->upload($fileIdData,'govId','img/patient/');
        
        $idFilename = $patientIdPhoto['filename'];
        $idStorageFilename = $patientIdPhoto['pathName'];

        $birthdate = new \DateTime($data['birthdate']);
        $this->auth->register(new RegisterPatientData(
            $data['name'], $data['address'], $data['email'], $data['contactNo'], $data['password'],
            $data['hospital'], $data['philhealthNo'], $data['contactGuard'], $data['guardianName'],$birthdate, $data['sex'],
            $idFilename,
            $idStorageFilename
            
        ));

        return $response->withHeader('Location', '/patient')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidator->make(PatientLoginRequestValidator::class)->validate(
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

        return $response->withHeader('Location', '/')->withStatus(302);
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