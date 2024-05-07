<?php 

declare(strict_types = 1);

namespace App\Controllers;

use Slim\Views\Twig;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Hospital;
use App\Enum\NavHospital;
use App\ResponseFormatter;
use App\Entity\AdmissionForm;
use App\Services\DoctorService;
use App\Entity\RequestAdmission;
use App\Services\PatientService;
use App\Services\RequestService;
use League\Flysystem\Filesystem;
use App\Services\HospitalService;
use App\Contracts\SessionInterface;
use App\Services\UploadFileService;
use App\DataObjects\RegisterDoctorData;
use App\Contracts\AuthHospitalInterface;
use App\DataObjects\RegisterPatientData;
use Psr\Http\Message\UploadedFileInterface;
use App\RequestValidators\UploadProofValidator;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\HospitalProviderServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\ChangeAdminPasswordValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\RegisterDoctorRequestValidator;
use App\RequestValidators\RegisterHospitalDoctorRequestValidator;
use App\RequestValidators\RegisterHospitalPatientRequestValidator;

class HospitalController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly HospitalProviderServiceInterface $hospitalProvider,
        private readonly AuthHospitalInterface $auth,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly HospitalService $hospitalService,
        private readonly DoctorService $doctorService,
        private readonly PatientService $patientService,
        private readonly UploadFileService $uploadFile,
    )
    {
    }
     
    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'hospital/index.twig',  ['isActive' => ['dashboard' => NavHospital::DASHBOARD],
        'doctor' => $this->hospitalService->approvedDoctorCount(),
        'patient' => $this->hospitalService->approvedPatientCount(),
        'doctorPending' => $this->hospitalService->pendingDoctorCount(),
        'patientPending' => $this->hospitalService->pendingPatientCount(),
    ]);
    }

    public function registerDoctorView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/register_doctor.twig',  ['isActive' => [
            'doctorRegister' => NavHospital::DOCTOR_REGISTER]]);
    }

    public function registerPatientView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/register_patient.twig',  ['isActive' => [
            'patientRegister' => NavHospital::PATIENT_REGISTER, ]]);
    }
    public function changePasswordView(Response $response): Response
    {
        return $this->twig->render($response, 'hospital/change_password.twig',  ['isActive' => ['changePassword' => TRUE]]);
    }

    public function doctorView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/hospital_doctor.twig',  ['isActive' => [
            'entity' => NavHospital::DOCTOR, 'entityDoctor' => TRUE]]);
    }
    
    public function patientView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/hospital_patient.twig',  ['isActive' => [
            'entity' => NavHospital::DOCTOR, 'entityPatient' => TRUE], 
            'doctors' => $this->doctorService->getDoctorNames(), 
            'hospitals' => $this->hospitalService->getReferralHospital(),
        ]);
    }

    public function doctorPendingView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/doctor_pending.twig',  ['isActive' => [
            'entityPending' => NavHospital::PENDING, 'pendingDoctor' => TRUE]]);
    }
    
    public function patientPendingView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/patient_pending.twig',  ['isActive' => [
            'entityPending' => NavHospital::PENDING, 'pendingPatient' => TRUE]]);
    }

    public function patientAcceptedArchiveView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/patient_accepted_archive.twig',  ['isActive' => [
            'archivePatient' => NavHospital::ARCHIVED_PATIENT,  'archiveAcceptedPatient' => TRUE]]);
    }

    public function doctorAcceptedArchiveView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/doctor_accepted_archive.twig',  ['isActive' => [
            'archiveDoctor' => NavHospital::ARCHIVED_DOCTOR, 'archiveAcceptedDoctor' => TRUE]]);
    }

    public function doctorDeclincedArchiveView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/doctor_declined_archive.twig',  ['isActive' => [
            'archiveDoctor' => NavHospital::ARCHIVED_DOCTOR, 'archiveDeclinedDoctor' => TRUE]]);
    }

    public function patientDeclinedArchiveView(Response $response): Response
    {

        return $this->twig->render($response, 'hospital/patient_declined_archive.twig',  ['isActive' => [
            'archivePatient' => NavHospital::ARCHIVED_PATIENT, 'archiveDeclinedPatient' => TRUE]]);
    }

    public function profile(Response $response): Response
    {
        $hospitalId = $this->session->get('hospital');  
        $hospitalData = $this->entityManagerService
            ->getRepository(Hospital::class)
            ->createQueryBuilder('h')
            ->select('h.id', 'h.name', 'h.address', 'h.email', 'h.contactNumber', 'h.hospitalStorageFilename', 'h.createdAt' )
            ->where('h.id = :id')
            ->setParameter(':id', $hospitalId)
            ->getQuery()
            ->getArrayResult();

        $hospital = [
            'name' => $hospitalData[0]['name'],
            'address' => $hospitalData[0]['address'],
            'email' => $hospitalData[0]['email'],
            'profilePicture' => $hospitalData[0]['hospitalStorageFilename'],
            'contactNumber' => $hospitalData[0]['contactNumber'],
            'createdAt' => $hospitalData[0]['createdAt']->format('m/d/Y g:i A'),
        ];


        return $this->twig->render($response, 'hospital/hospital_profile.twig', ['isActive' => ['profile' => NavHospital::PROFILE], 'hospital' => $hospital]);
    }

    public function registerDoctor(Request $request, Response $response): Response
    {
        // 1  Validated the data
        $data = $this->requestValidator->make(RegisterHospitalDoctorRequestValidator::class)->validate(
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

        $hospitalId = $this->session->get('hospital');

        $hospital = $this->hospitalProvider->getById((int)$hospitalId);
        
        $birthdate = new \DateTime($data['birthdate']);

        $this->doctorService->registerDoctor(new RegisterDoctorData(
            $data['name'], $data['password'], $birthdate, $data['sex'],  $data['address'], $hospital, 
            $data['email'], $data['contactNo'],
            $idFilename,
            $storageIdFilename, $empFilename, $storageEmpFilename
            
        ));

         $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>You added!</strong> The Doctor Added Succesfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';

        //3 display the data
        return $this->twig->render($response, 'hospital/register_doctor.twig', ['isActive' => ['register' => NavHospital::DOCTOR_REGISTER], 'html' => $html]);
    }

    public function registerPatient(Request $request, Response $response): Response
    {
            // 1  Validated the data
        $data = $this->requestValidator->make(RegisterHospitalPatientRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        /** @var UploadedFileInterface $fileData */
        // For Gov ID
        $fileIdData = $this->requestValidator->make(UploadProofValidator::class)->validate( $request->getUploadedFiles(), 'govId');
        
        $patientIdPhoto = $this->uploadFile->upload($fileIdData,'govId','img/patient/');
        
        $idFilename = $patientIdPhoto['filename'];
        $idStorageFilename = $patientIdPhoto['pathName'];

        $hospitalId = $this->session->get('hospital');

        $hospital = $this->hospitalProvider->getById((int)$hospitalId);

        $birthdate = new \DateTime($data['birthdate']);
        $this->patientService->registerPatient(new RegisterPatientData(
            $data['name'], $data['address'], $data['email'], $data['contactNo'], $data['password'],
            $hospital , $data['philhealthNo'], $data['contactGuard'], $data['guardianName'],$birthdate, $data['sex'],
            $idFilename,
            $idStorageFilename
            
        ));
        
         $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>You added!</strong> The Patient Added Succesfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';

        //3 display the data
        return $this->twig->render($response, 'hospital/register_patient.twig', ['isActive' => ['register' => NavHospital::PATIENT_REGISTER], 'html' => $html]);
    }

     public function hospitalDoctorLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $doctors  = $this->doctorService->getPaginatedDoctor($params);
        $transformer = function (Doctor $doctor) {
            $image = '<img src="/img/doctor/' .  $doctor->getProfilePicture() . '" width="75px" height="75px">';
            return [
                'id'                => $doctor->getId(),
                'name'              => $doctor->getName(),
                'email'             => $doctor->getEmail(),
                'profileImage'      => $image,
                'address'           => $doctor->getAddress(),
                'proofImage'        => $doctor->getStorageEmpFilename(),
                'doctorId'          => $doctor->getStorageIdFilename(),
                'contactNo'         => $doctor->getContact(),
                'createdAt'         => $doctor->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $doctor->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $doctor->getApprovedAt() ? $doctor->getApprovedAt()->format('m/d/Y g:i A') : $doctor->getApprovedAt(),
            ];
        };

        $totalDoctors = count($doctors);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $doctors->getIterator()),
            $params->draw,
            $totalDoctors
        );
    }

     public function doctorPendingLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $doctors  = $this->doctorService->getPaginatedPendingDoctor($params);
        $transformer = function (Doctor $doctor) {
            $image = '<img src="/img/doctor/' .  $doctor->getProfilePicture() . '" width="75px" height="75px">';
            return [
                'id'                => $doctor->getId(),
                'name'              => $doctor->getName(),
                'email'             => $doctor->getEmail(),
                'profileImage'      => $image,
                'address'           => $doctor->getAddress(),
                'proofImage'        => $doctor->getStorageEmpFilename(),
                'doctorId'          => $doctor->getStorageIdFilename(),
                'contactNo'         => $doctor->getContact(),
                'createdAt'         => $doctor->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $doctor->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $doctor->getApprovedAt() ? $doctor->getApprovedAt()->format('m/d/Y g:i A') : $doctor->getApprovedAt(),
            ];
        };

        $totalDoctors = count($doctors);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $doctors->getIterator()),
            $params->draw,
            $totalDoctors
        );
    }
    
     public function doctorAcceptedArchiveLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $doctors  = $this->doctorService->getPaginatedAcceptedArchiveDoctor($params);
        $transformer = function (Doctor $doctor) {
            $image = '<img src="/img/doctor/' .  $doctor->getProfilePicture() . '" width="75px" height="75px">';
            return [
                'id'                => $doctor->getId(),
                'name'              => $doctor->getName(),
                'email'             => $doctor->getEmail(),
                'profileImage'      => $image,
                'address'           => $doctor->getAddress(),
                'proofImage'        => $doctor->getStorageEmpFilename(),
                'doctorId'          => $doctor->getStorageIdFilename(),
                'contactNo'         => $doctor->getContact(),
                'createdAt'         => $doctor->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $doctor->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $doctor->getApprovedAt() ? $doctor->getApprovedAt()->format('m/d/Y g:i A') : $doctor->getApprovedAt(),
            ];
        };

        $totalDoctors = count($doctors);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $doctors->getIterator()),
            $params->draw,
            $totalDoctors
        );
    }

     public function doctorDeclinedArchiveLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $doctors  = $this->doctorService->getPaginatedDeclinedArchiveDoctor($params);
        $transformer = function (Doctor $doctor) {
            $image = '<img src="/img/doctor/' .  $doctor->getProfilePicture() . '" width="75px" height="75px">';
            return [
                'id'                => $doctor->getId(),
                'name'              => $doctor->getName(),
                'email'             => $doctor->getEmail(),
                'profileImage'      => $image,
                'address'           => $doctor->getAddress(),
                'proofImage'        => $doctor->getStorageEmpFilename(),
                'doctorId'          => $doctor->getStorageIdFilename(),
                'contactNo'         => $doctor->getContact(),
                'createdAt'         => $doctor->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $doctor->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $doctor->getApprovedAt() ? $doctor->getApprovedAt()->format('m/d/Y g:i A') : $doctor->getApprovedAt(),
            ];
        };

        $totalDoctors = count($doctors);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $doctors->getIterator()),
            $params->draw,
            $totalDoctors
        );
    }

     public function hospitalPatientLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $patients  = $this->patientService->getPaginatedPatient($params);
        $transformer = function (Patient $patient) {
            $image = '<img src="/img/patient/' .  $patient->getProfilePicture() . '" width="75px" height="75px">';
        return [
                'id'                => $patient->getId(),   
                'name'              => $patient->getName(),
                'email'             => $patient->getEmail(),
                'profileImage'      => $image,
                'proofImage'        => $patient->getIdStorageFilename(),
                'gender'           =>  $patient->getGender(),
                'address'           =>  $patient->getAddress(),
                'patientId'         => $patient->getIdStorageFilename(),
                'contactNo'         => $patient->getContact(),
                'createdAt'         => $patient->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $patient->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $patient->getApprovedAt() ? $patient->getApprovedAt()->format('m/d/Y g:i A') : $patient->getApprovedAt(),
            ];
        };

        $totalPatients = count($patients);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $patients->getIterator()),
            $params->draw,
            $totalPatients
        );
    }

     public function patientPendingLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $patients  = $this->patientService->getPaginatedPendingPatient($params);
        $transformer = function (Patient $patient) {
            $image = '<img src="/img/patient/' .  $patient->getProfilePicture() . '" width="75px" height="75px">';
        return [
                'id'                => $patient->getId(),
                'name'              => $patient->getName(),
                'email'             => $patient->getEmail(),
                'profileImage'      => $image,
                'proofImage'        => $patient->getIdStorageFilename(),
                'address'           => $patient->getAddress(),
                'patientId'         => $patient->getIdStorageFilename(),
                'contactNo'         => $patient->getContact(),
                'createdAt'         => $patient->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $patient->getUpdatedAt()->format('m/d/Y g:i A'),
            ];
        };

        $totalPatients = count($patients);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $patients->getIterator()),
            $params->draw,
            $totalPatients
        );
    }

    public function patientAcceptedArchiveLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $patients  = $this->patientService->getPaginatedAcceptedArchivePatient($params);
        $transformer = function (Patient $patient) {
            $image = '<img src="/img/patient/' .  $patient->getProfilePicture() . '" width="75px" height="75px">';
        return [
                'id'                => $patient->getId(),
                'name'              => $patient->getName(),
                'email'             => $patient->getEmail(),
                'profileImage'      => $image,
                'proofImage'        => $patient->getIdStorageFilename(),
                'address'           => $patient->getAddress(),
                'patientId'         => $patient->getIdStorageFilename(),
                'contactNo'         => $patient->getContact(),
                'createdAt'         => $patient->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $patient->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $patient->getApprovedAt() ? $patient->getApprovedAt()->format('m/d/Y g:i A') : $patient->getApprovedAt(),
            ];
        };

        $totalPatients = count($patients);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $patients->getIterator()),
            $params->draw,
            $totalPatients
        );
    }

    public function patienDeclinedArchiveLoad(Request $request, Response $response): Response
    {
        $params      = $this->requestService->getDataTableQueryParameters($request);
        $patients  = $this->patientService->getPaginatedDeclinedArchivePatient($params);
        $transformer = function (Patient $patient) {
            $image = '<img src="/img/patient/' .  $patient->getProfilePicture() . '" width="75px" height="75px">';
        return [
                'id'                => $patient->getId(),
                'name'              => $patient->getName(),
                'email'             => $patient->getEmail(),
                'profileImage'      => $image,
                'proofImage'        => $patient->getIdStorageFilename(),
                'address'           => $patient->getAddress(),
                'patientId'         => $patient->getIdStorageFilename(),
                'contactNo'         => $patient->getContact(),
                'createdAt'         => $patient->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt'         => $patient->getUpdatedAt()->format('m/d/Y g:i A'),
                'approveAt'         => $patient->getApprovedAt() ? $patient->getApprovedAt()->format('m/d/Y g:i A') : $patient->getApprovedAt(),
            ];
        };

        $totalPatients = count($patients);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($transformer, (array) $patients->getIterator()),
            $params->draw,
            $totalPatients
        );      
    }
    public function approvedToArchiveDoctor(Request $request, Response $response, Doctor $doctor): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($doctor->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        
        $this->entityManagerService->sync($this->doctorService->toArchive($doctor));


        return $response;
    }
    public function approveDoctor(Request $request, Response $response, Doctor $doctor): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($doctor->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }

        $this->entityManagerService->sync($this->doctorService->accept($doctor));


        return $response;
    }

    public function rejectDoctor(Response $response, Doctor $doctor): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($doctor->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->sync($this->doctorService->reject($doctor));


        return $response;
    }
    public function approvedToArchivePatient(Request $request, Response $response, Patient $patient): Response
    {
         $hospital = $request->getAttribute('hospital');
        if($patient->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }

        $this->entityManagerService->sync($this->patientService->toArchive($patient));


        return $response;
    }
       public function approvePatient(Request $request, Response $response, Patient $patient): Response
    {
         $hospital = $request->getAttribute('hospital');
        if($patient->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->sync($this->patientService->accept($patient));


        return $response;
    }

    public function rejectPatient(Request $request, Response $response, Patient $patient): Response
    { 
        $hospital = $request->getAttribute('hospital');
        if($patient->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->sync($this->patientService->reject($patient));


        return $response;
    }

    public function recoverAcceptedPatient(Request $request, Response $response, Patient $patient): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($patient->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->sync($this->patientService->recover($patient));


        return $response;
    }
    
    public function recoverAcceptedDoctor(Request $request, Response $response, Doctor $doctor): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($doctor->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->sync($this->doctorService->recover($doctor));


        return $response;
    }
    

    public function deleteAcceptedPatient(Request $request, Response $response, Patient $patient): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($patient->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->delete($patient, true);

        return $response;
    }
    
    public function deleteAcceptedDoctor(Request $request, Response $response, Doctor $doctor): Response
    {

        $hospital = $request->getAttribute('hospital');
        if($doctor->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->delete($doctor, true);

        return $response;
    }
        public function changePassword(Response $response, Request $request): Response
    {
        $data = $this->requestValidator->make(ChangeAdminPasswordValidator::class)->validate(
            $request->getParsedBody()
        );
         $hospital = $request->getAttribute('hospital');

       if(!password_verify($data['oldPassword'],  $hospital->getPassword()) ) {
         throw new ValidationException(['oldPassword' => ['Incorrect Password']]);
       }

         $hospital->setPassword(password_hash($data['newPassword'], PASSWORD_BCRYPT, ['cost' => 12]));

         $this->entityManagerService->sync($hospital);

         
        $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Password succesfully change</strong> 
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';

            return $this->twig->render($response, 'hospital/change_password.twig', ['isActive' => ['changePassword' => TRUE], 'html' => $html]);
    }



    

}