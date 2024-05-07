<?php 

declare(strict_types = 1);

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Slim\Views\Twig;
use App\Enum\ActiveNav;
use App\ResponseFormatter;
use App\Entity\AdmissionForm;
use App\Entity\RequestCheckup;
use App\Entity\RequestMedical;
use App\Entity\PrenatalCheckup;
use App\Entity\RequestAdmission;
use App\Services\RequestService;
use League\Flysystem\Filesystem;
use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\Services\UploadFileService;
use App\Services\CheckupFormService;
use App\Services\AdmissionFormService;
use App\Services\MedicalCertificateService;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\PatientProviderServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\RequestValidators\ChangeAdminPasswordValidator;
use Psr\Http\Message\ServerRequestInterface as Request;

class PatientController
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
        private readonly CheckupFormService $checkupFormService,
        private readonly AdmissionFormService $admissionFormService,
        private readonly MedicalCertificateService $medicalCertificateService,
        private readonly PatientProviderServiceInterface $patientProviderService,

    )
    {
    }
     
    public function index(Response $response): Response
    {
        return $this->twig->render($response, 'patient/index.twig',  ['isActive' => ['dashboard' => TRUE],
        'checkupRecord' => $this->checkupFormService->totalPatientForm(),     
        'admissionRecord' => $this->admissionFormService->totalPatientForm(),  
    ]);
    }
        public function profile(Response $response): Response
    {
        $patientId = $this->session->get('patient');
        $getPatient = $this->patientProviderService->getById($patientId);

        $patient = [
            'name' => $getPatient->getName(),
            'address' => $getPatient->getAddress(),
            'email' => $getPatient->getEmail(),
            'profilePicture' => $getPatient->getProfilePicture(),
            'contactNumber' => $getPatient->getContact(),
            'hospital' => $getPatient->getHospital()->getName(),
            'createdAt' => $getPatient->getApprovedAt()->format('m/d/Y g:i A'),
        ];


        return $this->twig->render($response, 'patient/patient_profile.twig', ['isActive' => ['profile' => TRUE], 'patient' => $patient]);
    }

    public function admissionForm(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'patient/patient_admission.twig',  ['isActive' => [
        'admissionRecord' => TRUE, 'admissionForm' => TRUE], ]);

    }
    public function checkupForm(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'patient/patient_checkup.twig',  ['isActive' => [
        'checkupRecord' => TRUE, 'checkupForm' => TRUE], ]);

    }
    public function medicalForm(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'patient/patient_medical.twig',  ['isActive' => [
        'medicalRecord' => TRUE, 'medicalForm' => TRUE], ]);

    }
    public function admissionRequestView(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'patient/admission_request.twig',  ['isActive' => [
        'admissionRecord' => TRUE, 'admissionRequest' => TRUE], ]);

    }

    public function checkupRequestView(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'patient/checkup_request.twig',  ['isActive' => [
        'checkupRecord' => TRUE, 'checkupRequest' => TRUE], ]);

    }
    public function changePasswordView(Response $response): Response
    {
        return $this->twig->render($response, 'patient/change_password.twig',  ['isActive' => ['changePassword' => TRUE]]);
    }

    public function medicalRequestView(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'patient/medical_request.twig',  ['isActive' => [
        'medicalRecord' => TRUE, 'medicalRequest' => TRUE], ]);

    }

        public function admissionLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $admissionForms  = $this->admissionFormService->getPaginatedPatientAdmission($params);
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
                    'request'     => $admissionForm->getRequested(),

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

        public function admissionRequestLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $admissionForms  = $this->admissionFormService->getPaginatedPatientRequestAdmission($params);
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
                    'request'     => $admissionForm->getRequested(),
                    'referralCode' => $admissionForm->getRequestAdmission()->getAdmissionCode(),
                    'requestDate'      => $admissionForm->getRequestAdmission()->getCreatedAt()->format('F j, Y'),
               

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


    public function requestAdmission(Request $request, Response $response, AdmissionForm $admissionForm): Response
    {
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        $admissionForm->setRequested(true);

        $this->entityManagerService->sync($admissionForm);

        $requestAdmission = new RequestAdmission();

        $requestAdmission->setAdmissionCode($code);
        $requestAdmission->setAdmissionForm($admissionForm);
        $this->entityManagerService->sync($requestAdmission);

        return $response;
    }   

    public function requestCheckup(Request $request, Response $response, PrenatalCheckup $prenatalCheckup): Response
    {
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        $prenatalCheckup->setRequested(true);

        $this->entityManagerService->sync($prenatalCheckup);

        $requestCheckup = new RequestCheckup();

        $requestCheckup->setCheckupCode($code);
        $requestCheckup->setPrenatalCheckup($prenatalCheckup);
        $this->entityManagerService->sync($requestCheckup);

        return $response;
    }   

    public function requestMedical(Request $request, Response $response, MedicalCertificate $medicalCertificate): Response
    {
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        $medicalCertificate->setRequested(true);

        $this->entityManagerService->sync($medicalCertificate);

        $requestMedical = new RequestMedical();

        $requestMedical->setMedicalCode($code);
        $requestMedical->setMedicalCertificate($medicalCertificate);
        $this->entityManagerService->sync($requestMedical);

        return $response;
    }   
    
    public function checkupformLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $prenatalCheckups  = $this->checkupFormService->getPaginatedPatientCheckup($params);
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
                    'fecalysis'   => $prenatalCheckup->getFecalysis() ? $prenatalCheckup->getFecalysis() : 'N/A',
                    'bloodCount'   => $prenatalCheckup->getBloodCount() ? $prenatalCheckup->getBloodCount() : 'N/A',
                    'prescription' => $prenatalCheckup->getIsPrescribed() ? 'Prescribed' : 'Pending Prescription',
                    'isPrescribed' => $prenatalCheckup->getIsPrescribed(),
                    'request'      => $prenatalCheckup->getRequested(),
                    
                    
   
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

    public function checkupRequestLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $prenatalCheckups  = $this->checkupFormService->getPaginatedRequestPatientCheckup($params);
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
                    'fecalysis'   => $prenatalCheckup->getFecalysis() ? $prenatalCheckup->getFecalysis() : 'N/A',
                    'bloodCount'   => $prenatalCheckup->getBloodCount() ? $prenatalCheckup->getBloodCount() : 'N/A',
                    'prescription' => $prenatalCheckup->getIsPrescribed() ? 'Prescribed' : 'Pending Prescription',
                    'isPrescribed' => $prenatalCheckup->getIsPrescribed(),
                    'request'      => $prenatalCheckup->getRequested(),
                    'referenceCode' => $prenatalCheckup->getRequestCheckup()->getCheckupCode(),
                    'requestDate'      => $prenatalCheckup->getRequestCheckup()->getCreatedAt()->format('F j, Y'),
   
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
    public function medicalformLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $medicalCertificates  = $this->medicalCertificateService->getPaginatedPatientMedical($params);
            $transformer = function (MedicalCertificate $medicalCertificate) {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($medicalCertificate->getPatient()->getBirthdate())->y;
            return [
                    'id'   => $medicalCertificate->getId(),
                    'patientAddress' => $medicalCertificate->getPatient()->getAddress(),
                    'patientGender' => $medicalCertificate->getPatient()->getGender(),
                    'patientAge'    => $age,
                    'patient' => $medicalCertificate->getPatient()->getName(),
                    'certificateDate' => $medicalCertificate->getCertificateDate()->format('F j, Y'),
                    'impression' => $medicalCertificate->getImpression(),
                    'purpose' => $medicalCertificate->getPurpose(),
                    'hospital' => $medicalCertificate->getHospital()->getName(),
                    'doctor' => $medicalCertificate->getDoctor()->getName(),
                    'hospitalAddress' => $medicalCertificate->getHospital()->getAddress(),
                    'request'      => $medicalCertificate->getRequested(),
                    
                    

                ];
            };

            $totalmedicalCertificates = count($medicalCertificates);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $medicalCertificates->getIterator()),
                $params->draw,
                $totalmedicalCertificates
            );
    }

    public function medicalRequestLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $medicalCertificates  = $this->medicalCertificateService->getPaginatedPatientRequestMedical($params);
            $transformer = function (MedicalCertificate $medicalCertificate) {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($medicalCertificate->getPatient()->getBirthdate())->y;
            return [
                    'id'   => $medicalCertificate->getId(),
                    'patientAddress' => $medicalCertificate->getPatient()->getAddress(),
                    'patientGender' => $medicalCertificate->getPatient()->getGender(),
                    'patientAge'    => $age,
                    'patient' => $medicalCertificate->getPatient()->getName(),
                    'certificateDate' => $medicalCertificate->getCertificateDate()->format('F j, Y'),
                    'impression' => $medicalCertificate->getImpression(),
                    'purpose' => $medicalCertificate->getPurpose(),
                    'hospital' => $medicalCertificate->getHospital()->getName(),
                    'doctor' => $medicalCertificate->getDoctor()->getName(),
                    'hospitalAddress' => $medicalCertificate->getHospital()->getAddress(),
                    'request'      => $medicalCertificate->getRequested(),
                    'referenceCode'      => $medicalCertificate->getRequestMedical()->getMedicalCode(),
                    'requestDate'      => $medicalCertificate->getRequestMedical()->getCreatedAt()->format('F j, Y'),
                    

                ];
            };

            $totalmedicalCertificates = count($medicalCertificates);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $medicalCertificates->getIterator()),
                $params->draw,
                $totalmedicalCertificates
            );
    }

      public function getPrescriptionImage(Request $request, Response $response,  PrenatalCheckup $prenatalCheckup): Response
     {
         return $this->responseFormatter->asJson($response, ['prescriptionImage' => $prenatalCheckup->getPrescription()->getStorageFilename()]);
     }
     public function getReferral(Request $request, Response $response,  AdmissionForm $admissionForm): Response
     {
      // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);


        // HTML content for the medical record with Bootstrap and footer
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Medical Record</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .hospital-info {
                text-align: center;
                margin-bottom: 20px;
            }
            .content {
                margin: 20px;
            }
            .footer {
                position: fixed;
                bottom: 20px;
                left: 0;
                right: 0;
                text-align: center;
                color: gray;
            }
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Admission Form Request</h1>
                    <p>Date of Request: '. $admissionForm->getRequestAdmission()->getCreatedAt()->format('F j, Y') .'  </p>
                </div>
                <div class="hospital-info">
                    <p>'.$admissionForm->getHospital()->getName() .'</p>
                    <p> '.$admissionForm->getHospital()->getAddress() .'</p>
                    <p> '.$admissionForm->getHospital()->getContactNumber() .' </p>
                </div>
                <div class="content container">
                    <!-- Your medical record content goes here -->
                    <p>Please provide the reference number to '.$admissionForm->getHospital()->getName() .' Health Facility for the service of your document</p>
                    <p><b>Reference Number:</b> '. $admissionForm->getRequestAdmission()->getAdmissionCode() .'</p>
                </div>
            </div>
            <div class="footer">
                <p>This document does not serve as an official document and cannot be used for any legal purposes.</p>
            </div>
        </body>
        </html>
        ';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

            // $imagePath = __DIR__ . '/../../public/images/a02c1abe8f8f7555.png'; // Adjust path as needed
            // $imageData = file_get_contents($imagePath);
            // $base64Image = 'data:image/png;base64,' . base64_encode($imageData);

            // $dompdf->getCanvas()->image($base64Image, 100, 100, 100, 100); // Adjust position and size


        // Render PDF (watermark and footer will be included)
        $dompdf->render();

            // Output PDF to browser
            // $time = new \DateTime();
            // (string)$timestop = $time->format('m/d/Y');
            // $text =  $admissionForm->getPatient()->getName();
            // $fullName = explode(' ', $text);
            // $firstName = trim($fullName[0]); 
            $dompdf->stream('AdmissionRecord'. $admissionForm->getRequestAdmission()->getAdmissionCode() . $admissionForm->getHospital()->getName() .'.pdf', array('Attachment' => 0));
     
}
     public function getCheckupReference(Request $request, Response $response,  PrenatalCheckup $prenatalCheckup): Response
     {
      // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);


        // HTML content for the medical record with Bootstrap and footer
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Medical Record</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .hospital-info {
                text-align: center;
                margin-bottom: 20px;
            }
            .content {
                margin: 20px;
            }
            .footer {
                position: fixed;
                bottom: 20px;
                left: 0;
                right: 0;
                text-align: center;
                color: gray;
            }
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1> Checkup Form Request</h1>
                </div>
                <div class="hospital-info">
                    <p>'.$prenatalCheckup->getHospital()->getName() .'</p>
                    <p> '.$prenatalCheckup->getHospital()->getAddress() .'</p>
                    <p> '.$prenatalCheckup->getHospital()->getContactNumber() .' </p>
                </div>
                <div class="content container">
                    <!-- Your medical record content goes here -->
                    <p>Please provide the reference number to '.$prenatalCheckup->getHospital()->getName() .' Health Facility for the service of your document</p>
                    <p><b>Reference Number:</b> '. $prenatalCheckup->getRequestCheckup()->getCheckupCode() .'</p>
                </div>
            </div>
            <div class="footer">
                <p>This document does not serve as an official document and cannot be used for any legal purposes.</p>
            </div>
        </body>
        </html>
        ';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

            // $imagePath = __DIR__ . '/../../public/images/a02c1abe8f8f7555.png'; // Adjust path as needed
            // $imageData = file_get_contents($imagePath);
            // $base64Image = 'data:image/png;base64,' . base64_encode($imageData);

            // $dompdf->getCanvas()->image($base64Image, 100, 100, 100, 100); // Adjust position and size


        // Render PDF (watermark and footer will be included)
        $dompdf->render();

            // Output PDF to browser
            // $time = new \DateTime();
            // (string)$timestop = $time->format('m/d/Y');
            // $text =  $admissionForm->getPatient()->getName();
            // $fullName = explode(' ', $text);
            // $firstName = trim($fullName[0]); 
            $dompdf->stream('CheckupForm'. $prenatalCheckup->getHospital()->getName() .'.pdf', array('Attachment' => 0));
     
}

     public function getMedicalReference(Request $request, Response $response,  MedicalCertificate $medicalCertificate): Response
     {
      // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);


        // HTML content for the medical record with Bootstrap and footer
        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Medical Record</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .hospital-info {
                text-align: center;
                margin-bottom: 20px;
            }
            .content {
                margin: 20px;
            }
            .footer {
                position: fixed;
                bottom: 20px;
                left: 0;
                right: 0;
                text-align: center;
                color: gray;
            }
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Medical Certificate Request</h1>
                    <p>Date of Request: '. $medicalCertificate->getRequestMedical()->getCreatedAt()->format('F j, Y') .'  </p>
                </div>
                <div class="hospital-info">
                    <p>'.$medicalCertificate->getHospital()->getName() .'</p>
                    <p> '.$medicalCertificate->getHospital()->getAddress() .'</p>
                    <p> '.$medicalCertificate->getHospital()->getContactNumber() .' </p>
                </div>
                <div class="content container">
                    <!-- Your medical record content goes here -->
                    <p>Please provide the reference number to '.$medicalCertificate->getHospital()->getName() .' Health Facility for the service of your document</p>
                    <p><b>Reference Number:</b> '. $medicalCertificate->getRequestMedical()->getMedicalCode() .'</p>
                </div>
            </div>
            <div class="footer">
                <p>This document does not serve as an official document and cannot be used for any legal purposes.</p>
            </div>
        </body>
        </html>
        ';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

            // $imagePath = __DIR__ . '/../../public/images/a02c1abe8f8f7555.png'; // Adjust path as needed
            // $imageData = file_get_contents($imagePath);
            // $base64Image = 'data:image/png;base64,' . base64_encode($imageData);

            // $dompdf->getCanvas()->image($base64Image, 100, 100, 100, 100); // Adjust position and size


        // Render PDF (watermark and footer will be included)
        $dompdf->render();

            // Output PDF to browser
            // $time = new \DateTime();
            // (string)$timestop = $time->format('m/d/Y');
            // $text =  $admissionForm->getPatient()->getName();
            // $fullName = explode(' ', $text);
            // $firstName = trim($fullName[0]); 
            $dompdf->stream('medicalCertificate'. $medicalCertificate->getHospital()->getName() .'.pdf', array('Attachment' => 0));
     
}
    public function changePassword(Response $response, Request $request): Response
    {
        $data = $this->requestValidator->make(ChangeAdminPasswordValidator::class)->validate(
            $request->getParsedBody()
        );
         $patient = $request->getAttribute('patient');

       if(!password_verify($data['oldPassword'],  $patient->getPassword()) ) {
         throw new ValidationException(['oldPassword' => ['Incorrect Password']]);
       }

         $patient->setPassword(password_hash($data['newPassword'], PASSWORD_BCRYPT, ['cost' => 12]));

         $this->entityManagerService->sync($patient);

         
        $html =  '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Password succesfully change</strong> 
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';

            return $this->twig->render($response, 'patient/change_password.twig', ['isActive' => ['changePassword' => TRUE], 'html' => $html]);
    }

}