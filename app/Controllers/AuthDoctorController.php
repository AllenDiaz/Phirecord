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
use App\Contracts\AuthDoctorInterface;
use App\Exception\ValidationException;
use App\DataObjects\RegisterDoctorData;
use Psr\Http\Message\UploadedFileInterface;
use App\RequestValidators\UploadProofValidator;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\DoctorLoginRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\RegisterDoctorRequestValidator;
use App\RequestValidators\TwoFactorLoginRequestValidator;

class AuthDoctorController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly HospitalService $hospitalService,
        private readonly UploadFileService $uploadFile,
        private readonly AuthDoctorInterface $auth,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly EntityManagerServiceInterface $entityManager,
        private readonly ResponseFormatter $responseFormatter
    )
    {
    }

    public function loginView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/doctor_login.twig');
    }

    public function registerView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/doctor_register.twig',  ['hospitals' => $this->hospitalService->getHospitalNames()]);
    }
    
    public function register(Request $request, Response $response): Response
    {
        // 1  Validated the data
        $data = $this->requestValidator->make(RegisterDoctorRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        /** @var UploadedFileInterface $fileData */
        // For Gov ID
        $fileIdData = $this->requestValidator->make(UploadProofValidator::class)->validate( $request->getUploadedFiles(), 'govId');
        
        $doctorIdPhoto = $this->uploadFile->upload($fileIdData,'govId','img/doctor/');
        
        $idFilename = $doctorIdPhoto['filename'];
        $storageIdFilename = $doctorIdPhoto['pathName'];

        // For Doctor Proof of employment 
        $fileProofData = $this->requestValidator->make(UploadProofValidator::class)->validate($request->getUploadedFiles(), 'proof');
        
        $doctorProofPhoto = $this->uploadFile->upload($fileProofData, 'proof', 'img/doctor/');
        
        $empFilename = $doctorProofPhoto['filename'];
        $storageEmpFilename = $doctorProofPhoto['pathName'];
        
        $birthdate = new \DateTime($data['birthdate']);
        $this->auth->register(new RegisterDoctorData(
            $data['name'], $data['password'], $birthdate, $data['sex'],  $data['address'], $data['hospital'], $data['email'], $data['contactNo'],
            $idFilename,
            $storageIdFilename, $empFilename, $storageEmpFilename
            
        ));

        return $response->withHeader('Location', '/doctor')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidator->make(DoctorLoginRequestValidator::class)->validate(
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

        return $response->withHeader('Location', '/doctor/login')->withStatus(302);
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