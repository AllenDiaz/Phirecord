<?php 

declare(strict_types = 1);

namespace App\Controllers;

use Slim\Views\Twig;
use App\Enum\ActiveNav;
use App\ResponseFormatter;
use App\Entity\AdmissionForm;
use App\Entity\PrenatalCheckup;
use App\Services\DoctorService;
use App\Services\RequestService;
use League\Flysystem\Filesystem;
use App\Services\HospitalService;
use App\Contracts\SessionInterface;
use App\Services\UploadFileService;
use App\Services\CheckupFormService;
use App\Services\AdmissionFormService;
use Psr\Http\Message\UploadedFileInterface;
use App\RequestValidators\UploadProofValidator;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\DoctorProviderServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\ChangeAdminPasswordValidator;
use Psr\Http\Message\ServerRequestInterface as Request;

class DoctorController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Twig $twig,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly RequestValidatorFactoryInterface $requestValidator,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly UploadFileService $uploadFile,
        private readonly DoctorService $doctorService,
        private readonly DoctorProviderServiceInterface $doctorProviderService,
        private readonly AdmissionFormService $admissionFormService,
        private readonly CheckupFormService $checkupFormService,

    )
    {
    }
     
    public function index(Response $response): Response
    {

        $doctorId = $this->session->get('doctor');
        $doctor = $this->doctorProviderService->getById($doctorId);
        $hospitalName = $doctor->getHospital()->getName();

        return $this->twig->render($response, 'doctor/index.twig',  ['isActive' => ['dashboard' => ActiveNav::DASHBOARD],
        'hospital' => ['name' => $hospitalName],
        'doctor' => $this->doctorService->totalDoctorCount(),
        'patient' => $this->doctorService->approvedPatientCount(),
        // 'doctorPending' => $this->doctorService->pendingDoctorCount(),
        // 'patientPending' => $this->doctorService->pendingPatientCount(),

        ]
    
    );
    }
    public function profile(Response $response): Response
    {
        $doctorId = $this->session->get('doctor');
        $getDoctor = $this->doctorProviderService->getById($doctorId);

        $doctor = [
            'name' => $getDoctor->getName(),
            'address' => $getDoctor->getAddress(),
            'email' => $getDoctor->getEmail(),
            'profilePicture' => $getDoctor->getProfilePicture(),
            'contactNumber' => $getDoctor->getContact(),
            'hospital' => $getDoctor->getHospital()->getName(),
            'createdAt' => $getDoctor->getApprovedAt()->format('m/d/Y g:i A'),
        ];


        return $this->twig->render($response, 'doctor/doctor_profile.twig', ['isActive' => ['profile' => TRUE], 'doctor' => $doctor]);
    }
    public function changePasswordView(Response $response): Response
    {
        return $this->twig->render($response, 'doctor/change_password.twig',  ['isActive' => ['changePassword' => TRUE]]);
    }

    public function admissionForm(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'doctor/patient_admission.twig',  ['isActive' => [
        'record' => TRUE, 'admissionForm' => TRUE], ]);

    }

    public function pendingPrescription(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'doctor/patient_pending_prescription.twig',  ['isActive' => [
        'checkup' => TRUE, 'pendingPrescription' => TRUE]]);

    }

    public function prescription(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'doctor/patient_prescription.twig',  ['isActive' => [
        'checkup' => TRUE, 'prescription' => TRUE]]);

    }

    public function admissionLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $admissionForms  = $this->admissionFormService->getPaginatedDoctorAdmission($params);
            $transformer = function (AdmissionForm $admissionForm) {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($admissionForm->getPatient()->getBirthdate())->y;

            return [
                    'id'   => $admissionForm->getId(),
                    'patient' => $admissionForm->getPatient()->getName(),
                    'patientAddress' => $admissionForm->getPatient()->getAddress(),
                    'patientGender' => $admissionForm->getPatient()->getGender(),
                    'patientAge'    => $age,
                    'admissionDate' => $admissionForm->getAdmissionDate()->format('F j, Y'),
                    'hospital' => $admissionForm->getHospital()->getName(),
                    'hospitalAddress' => $admissionForm->getHospital()->getAddress(),
                    'doctor' => $admissionForm->getDoctor()->getName(),
                    'symptoms'   => $admissionForm->getSymptoms(),
                    'bloodPressure'   => $admissionForm->getBloodPressure(),
                    'temperature'   => $admissionForm->getTemperature(),
                    'weight'   => $admissionForm->getWeight(),
                    'respiratoryRate'   => $admissionForm->getRespiratoryRate(),
                    'pulseRate'   => $admissionForm->getPulseRate(),
                    'oxygenSaturation'   => $admissionForm->getOxygenSaturation(),
                    'diagnosis'   => $admissionForm->getDiagnosis(),

                ];
            };

            $totalAdmissionForms = count($admissionForms);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $admissionForms->getIterator()),
                $params->draw,
                $totalAdmissionForms
            );
    }

    public function pendingPrescriptionLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $prenatalCheckups  = $this->checkupFormService->getPaginatedDoctorPendingPrescription($params);
            $transformer = function (PrenatalCheckup $prenatalCheckup) {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($prenatalCheckup->getPatient()->getBirthdate())->y;

            return [
                    'id'   => $prenatalCheckup->getId(),
                    'patient' => $prenatalCheckup->getPatient()->getName(),
                    'patientAddress' => $prenatalCheckup->getPatient()->getAddress(),
                    'patientGender' => $prenatalCheckup->getPatient()->getGender(),
                    'patientAge'    => $age,
                    'checkupDate' => $prenatalCheckup->getCheckupDate()->format('F j, Y'),
                    'menstrualDate' => $prenatalCheckup->getLastMenstrualDate() ? $prenatalCheckup->getLastMenstrualDate()->format('F j, Y') : 'N/A',
                    'hospital' => $prenatalCheckup->getHospital()->getName(),
                    'hospitalAddress' => $prenatalCheckup->getHospital()->getAddress(),
                    'doctor' => $prenatalCheckup->getDoctor()->getName(),
                    'familyMember'   => $prenatalCheckup->getFamilyMember(),
                    'confineDate'   => $prenatalCheckup->getConfineDateEstimated()->format('F j, Y'),
                    'fetal'   => $prenatalCheckup->getFetalHeartTones() ? $prenatalCheckup->getFetalHeartTones()  : 'N/A',
                    'gravida'   => $prenatalCheckup->getGravida() ? $prenatalCheckup->getGravida() : 'N/A',
                    'para'   => $prenatalCheckup->getPara() ? $prenatalCheckup->getPara() : 'N/A',
                    'labaratory'   => $prenatalCheckup->getLabaratory() ? $prenatalCheckup->getLabaratory() : 'N/A',
                    'urinalysis'   => $prenatalCheckup->getUrinalysis() ? $prenatalCheckup->getUrinalysis() : 'N/A',
                    'bloodCount'   => $prenatalCheckup->getBloodCount() ? $prenatalCheckup->getBloodCount() : 'N/A',
                    'fecalysis'   => $prenatalCheckup->getFecalysis() ? $prenatalCheckup->getFecalysis() : 'N/A',
                    
   
                ];
            };

            $totalPrenatalCheckups = count($prenatalCheckups);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $prenatalCheckups->getIterator()),
                $params->draw,
                $totalPrenatalCheckups
            );
    }

    public function prescriptionLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
        $prenatalCheckups  = $this->checkupFormService->getPaginatedDoctorPrescription($params);
            $transformer = function (PrenatalCheckup $prenatalCheckup) {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($prenatalCheckup->getPatient()->getBirthdate())->y;

            return [
                    'id'   => $prenatalCheckup->getId(),
                    'patient' => $prenatalCheckup->getPatient()->getName(),
                    'patientAddress' => $prenatalCheckup->getPatient()->getAddress(),
                    'patientGender' => $prenatalCheckup->getPatient()->getGender(),
                    'patientAge'    => $age,
                    'checkupDate' => $prenatalCheckup->getCheckupDate()->format('F j, Y'),
                    'menstrualDate' => $prenatalCheckup->getLastMenstrualDate() ? $prenatalCheckup->getLastMenstrualDate()->format('F j, Y') : 'N/A',
                    'hospital' => $prenatalCheckup->getHospital()->getName(),
                    'hospitalAddress' => $prenatalCheckup->getHospital()->getAddress(),
                    'doctor' => $prenatalCheckup->getDoctor()->getName(),
                    'familyMember'   => $prenatalCheckup->getFamilyMember(),
                    'confineDate'   => $prenatalCheckup->getConfineDateEstimated()->format('F j, Y'),
                    'fetal'   => $prenatalCheckup->getFetalHeartTones() ? $prenatalCheckup->getFetalHeartTones()  : 'N/A',
                    'gravida'   => $prenatalCheckup->getGravida() ? $prenatalCheckup->getGravida() : 'N/A',
                    'para'   => $prenatalCheckup->getPara() ? $prenatalCheckup->getPara() : 'N/A',
                    'labaratory'   => $prenatalCheckup->getLabaratory() ? $prenatalCheckup->getLabaratory() : 'N/A',
                    'urinalysis'   => $prenatalCheckup->getUrinalysis() ? $prenatalCheckup->getUrinalysis() : 'N/A',
                    'bloodCount'   => $prenatalCheckup->getBloodCount() ? $prenatalCheckup->getBloodCount() : 'N/A',
                    'fecalysis'   => $prenatalCheckup->getFecalysis() ? $prenatalCheckup->getFecalysis() : 'N/A',
                    'prescriptionId' => $prenatalCheckup->getPrescription()->getId(),
                    'prescriptionImage' => $prenatalCheckup->getPrescription()->getStorageFilename(),
                    
   
                ];
            };

            $totalPrenatalCheckups = count($prenatalCheckups);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $prenatalCheckups->getIterator()),
                $params->draw,
                $totalPrenatalCheckups
            );
    }

    public function storePrescription(Request $request, Response $response, PrenatalCheckup $prenatalCheckup): Response
    {
        /** @var UploadedFileInterface $fileData */
        // For Gov ID
        $fileIdData = $this->requestValidator->make(UploadProofValidator::class)->validate( $request->getUploadedFiles(), 'prescription');
        
        $prescriptionPhoto = $this->uploadFile->upload($fileIdData,'prescription','img/prescription/');
        
        $storageIdFilename = $prescriptionPhoto['pathName'];

        $prescription = $this->checkupFormService->create($prenatalCheckup, $storageIdFilename);
        $prenatalCheckup->setIsPrescribed(TRUE);
        

        $this->entityManagerService->sync($prenatalCheckup);
        $this->entityManagerService->sync($prescription);

        return $response;
    }
            public function changePassword(Response $response, Request $request): Response
    {
        $data = $this->requestValidator->make(ChangeAdminPasswordValidator::class)->validate(
            $request->getParsedBody()
        );
         $doctor = $request->getAttribute('doctor');

       if(!password_verify($data['oldPassword'],  $doctor->getPassword()) ) {
         throw new ValidationException(['oldPassword' => ['Incorrect Password']]);
       }

         $doctor->setPassword(password_hash($data['newPassword'], PASSWORD_BCRYPT, ['cost' => 12]));

         $this->entityManagerService->sync($doctor);

         
        $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Password succesfully change</strong> 
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';

            return $this->twig->render($response, 'doctor/change_password.twig', ['isActive' => ['changePassword' => TRUE], 'html' => $html]);
    }


    
}