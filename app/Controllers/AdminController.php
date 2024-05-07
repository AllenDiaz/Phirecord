<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Session;
use App\Enum\Status;
use Slim\Views\Twig;
use App\Entity\Admin;
use App\Enum\ActiveNav;
use App\Entity\Hospital;
use App\ResponseFormatter;
use App\Services\AdminService;
use App\Services\RequestService;
use League\Flysystem\Filesystem;
use App\Services\HospitalService;
use App\Contracts\SessionInterface;
use App\Services\UploadFileService;
use App\Contracts\AuthAdminInterface;
use App\Exception\ValidationException;
use Psr\Http\Message\UploadedFileInterface;
use App\DataObjects\RegisterAdminHospitalData;
use App\Contracts\AdminProviderServiceInterface;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\ChangeAdminPasswordValidator;
use App\RequestValidators\UploadHospitalProofValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\RegisterHospitalRequestValidator;

class AdminController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly AdminProviderServiceInterface $adminProvider,
        private readonly AuthAdminInterface $auth,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly HospitalService $hospitalService,
        private readonly UploadFileService $uploadFile,
        private readonly AdminService $adminService,


    )
    {
    }

    public function index(Response $response): Response
    {


        return $this->twig->render($response, 'admin/index.twig',  ['isActive' => ['dashboard' => ActiveNav::DASHBOARD],
        'hospital' => $this->adminService->approvedHospitalCount(), 
        'doctor' => $this->adminService->approvedDoctorCount(),
        'patient' => $this->adminService->approvedPatientCount(),
        'hospitalPending' => $this->adminService->pendingHospitalCount(), 
        'doctorPending' => $this->adminService->pendingDoctorCount(),
        'patientPending' => $this->adminService->pendingPatientCount(),
        
        ]);
    }

    public function indexHead(Response $response): Response
    {
        return $this->twig->render($response, 'head_admin/index.twig',  ['isActive' => ['dashboard' => ActiveNav::DASHBOARD]]);
    }

    public function registerFacilityView(Request $request, Response $response): Response
    {


        return $this->twig->render($response, 'admin/admin_hospital.twig', ['isActive' => ['register' => ActiveNav::REGISTER]]);
    }

    public function registeredHospitalView(Response $response): Response
    {
        return $this->twig->render($response, 'admin/registered_hospital.twig',  ['isActive' => ['registered' => ActiveNav::REGISTERED]]);
    }

    public function changePasswordView(Response $response): Response
    {
        return $this->twig->render($response, 'admin/change_password.twig',  ['isActive' => ['changePassword' => TRUE]]);
    }

    public function approveFacilityView(Request $request, Response $response): Response
    {


        return $this->twig->render($response, 'admin/approved_hospital.twig', ['isActive' => ['request' => ActiveNav::REQUEST, 'approve' => TRUE]]);
    }   

    public function pendingFacilityView(Request $request, Response $response): Response
    {


        return $this->twig->render($response, 'admin/pending_hospital.twig', ['isActive' => ['request' => ActiveNav::REQUEST, 'decline' => TRUE]]);
    }  

    public function approvedArchiveView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'admin/approved_archive.twig', ['isActive' => ['archived' => ActiveNav::ARCHIVED, 'archiveApprove' => TRUE]]);
    }   
    
    public function declinedArchiveView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'admin/declined_archive.twig', ['isActive' => ['archived' => ActiveNav::ARCHIVED, 'archiveDecline' => TRUE]]);
    }   


    public function profile(Response $response): Response
    {
        $adminId = $this->session->get('admin');  
        $adminData = $this->entityManagerService
            ->getRepository(Admin::class)
            ->createQueryBuilder('a')
            ->select('a.id', 'a.name', 'a.address', 'a.email', 'a.contactNumber', 'a.filename', 'a.storageFilename', 'a.birthdate', 'a.gender', 'a.profilePicture')
            ->where('a.id = :id')
            ->setParameter(':id', $adminId)
            ->getQuery()
            ->getArrayResult();
        // var_dump($adminData);

        $admin = [
            'name' => $adminData[0]['name'],
            'address' => $adminData[0]['address'],
            'email' => $adminData[0]['email'],
            'filename' => $adminData[0]['filename'],
            'storageFilename' => $adminData[0]['storageFilename'],
            'contactNumber' => $adminData[0]['contactNumber'],
            'birthdate' => $adminData[0]['birthdate'],
            'gender' => $adminData[0]['gender'],
            'profileImage' => $adminData[0]['profilePicture'],
        ];


        return $this->twig->render($response, 'admin/admin_profile.twig', ['isActive' => ['profile' => ActiveNav::PROFILE], 'admin' => $admin]);
    }

    public function registerFacility(Request $request, Response $response): Response
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
        

        $this->auth->registerHospital(new RegisterAdminHospitalData(
            $data['name'], $data['address'], $data['email'], $data['contactNo'],
            $data['password'], $filenameProof,
            $storageNameProof, $filenameProfile, $storageNameProfile
            
        ));

        $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>You added!</strong> The Hospital Added Succesfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';


        //3 display the data
        return $this->twig->render($response, 'admin/admin_hospital.twig', ['isActive' => ['register' => ActiveNav::REGISTER], 'html' => $html]);
    }

    public function loadRegistered(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $hospitals  = $this->hospitalService->getPaginatedRegisteredHospital($params);
        $transformer = function (Hospital $hospital) {
            $image = '<img src="/img/hospital/' .  $hospital->getHospitalStorageFilename() . '" width="75px" height="75px">';
            return [
                'id'                => $hospital->getId(),
                'name'              => $hospital->getName(),
                'email'             => $hospital->getEmail(),
                'profileImage'      => $image,
                'address'           => $hospital->getAddress(),
                'proofImage'        => $hospital->getStorageFilename(),
                'contactNo'         => $hospital->getContactNumber(),
                'createdAt'         => $hospital->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $hospital->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $hospital->getApprovedAt() ? $hospital->getApprovedAt()->format('m/d/Y g:i A') : $hospital->getApprovedAt(),
            ];
        };

        $totalHospitals = count($hospitals);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $hospitals->getIterator()),
            $params->draw,
            $totalHospitals
        );
    }

    public function loadPending(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $hospitals  = $this->hospitalService->getPaginatedPendingHospital($params);
        $transformer = function (Hospital $hospital) {
            $image = '<img src="/img/hospital/' .  $hospital->getHospitalStorageFilename() . '" width="75px" height="75px">';
            return [
                'id'                => $hospital->getId(),
                'name'              => $hospital->getName(),
                'email'             => $hospital->getEmail(),
                'profileImage'      => $image,
                'address'           => $hospital->getAddress(),
                'proofImage'        => $hospital->getStorageFilename(),
                'contactNo'         => $hospital->getContactNumber(),
                'createdAt'         => $hospital->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $hospital->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $hospital->getApprovedAt() ? $hospital->getApprovedAt()->format('m/d/Y g:i A') : $hospital->getApprovedAt(),
            ];
        };

        $totalHospitals = count($hospitals);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $hospitals->getIterator()),
            $params->draw,
            $totalHospitals
        );
    }

    public function loadArchiveApproved(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $hospitals  = $this->hospitalService->getPaginatedArchiveApproveHospital($params);
        $transformer = function (Hospital $hospital) {
            $image = '<img src="/img/hospital/' .  $hospital->getHospitalStorageFilename() . '" width="75px" height="75px">';
            return [
                'id'                => $hospital->getId(),
                'name'              => $hospital->getName(),
                'email'             => $hospital->getEmail(),
                'profileImage'      => $image,
                'address'           => $hospital->getAddress(),
                'proofImage'        => $hospital->getStorageFilename(),
                'contactNo'         => $hospital->getContactNumber(),
                'createdAt'         => $hospital->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $hospital->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $hospital->getApprovedAt() ? $hospital->getApprovedAt()->format('m/d/Y g:i A') : $hospital->getApprovedAt(),
            ];
        };

        $totalHospitals = count($hospitals);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $hospitals->getIterator()),
            $params->draw,
            $totalHospitals
        );
    }

    public function loadArchiveDeclined(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $hospitals  = $this->hospitalService->getPaginatedArchiveDeclinedHospital($params);
        $transformer = function (Hospital $hospital) {
            $image = '<img src="/img/hospital/' .  $hospital->getHospitalStorageFilename() . '" width="75px" height="75px">';
            return [
                'id'                => $hospital->getId(),
                'name'              => $hospital->getName(),
                'email'             => $hospital->getEmail(),
                'profileImage'      => $image,
                'address'           => $hospital->getAddress(),
                'proofImage'        => $hospital->getStorageFilename(),
                'contactNo'         => $hospital->getContactNumber(),
                'createdAt'         => $hospital->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $hospital->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $hospital->getApprovedAt() ? $hospital->getApprovedAt()->format('m/d/Y g:i A') : $hospital->getApprovedAt(),
            ];
        };

        $totalHospitals = count($hospitals);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $hospitals->getIterator()),
            $params->draw,
            $totalHospitals
        );
    }

    public function acceptHospital(Response $response, Hospital $hospital): Response
    {
        $this->entityManagerService->sync($this->hospitalService->activateStatus($hospital));


        return $response;
    }

    public function approvedArchiveHospital(Response $response, Hospital $hospital): Response
    {
        $this->entityManagerService->sync($this->hospitalService->toArchive($hospital));


        return $response;
    }
    public function declinedArchiveHospital(Response $response, Hospital $hospital): Response
    {
        $this->entityManagerService->sync($this->hospitalService->pendingToArchive($hospital));


        return $response;
    }

    public function deleteRegisterHospital(Response $response, Hospital $hospital): Response
    {
        $this->entityManagerService->delete($hospital, true);

        return $response;
    }

    public function deleteDeclinedHospital(Response $response, Hospital $hospital): Response
    {
        $this->entityManagerService->delete($hospital, true);

        return $response;
    }

    public function recoverDeclinedHospital(Response $response, Hospital $hospital): Response
    {
       $this->entityManagerService->sync($this->hospitalService->archiveToPending($hospital));

        return $response;
    }

    public function recoverApprovedHospital(Response $response, Request $request, Hospital $hospital): Response
    {
        $admin = $request->getAttribute('admin');

       $this->entityManagerService->sync($this->hospitalService->archiveToApproved($hospital));

        return $response;
    }
    public function changePassword(Response $response, Request $request): Response
    {
        $data = $this->requestValidator->make(ChangeAdminPasswordValidator::class)->validate(
            $request->getParsedBody()
        );
         $admin = $request->getAttribute('admin');

       if(!password_verify($data['oldPassword'],  $admin->getPassword()) ) {
         throw new ValidationException(['oldPassword' => ['Incorrect Password']]);
       }

         $admin->setPassword(password_hash($data['newPassword'], PASSWORD_BCRYPT, ['cost' => 12]));

         $this->entityManagerService->sync($admin);

         
        $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Password succesfully change</strong> 
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';

            return $this->twig->render($response, 'admin/change_password.twig', ['isActive' => ['changePassword' => TRUE], 'html' => $html]);
    }




}