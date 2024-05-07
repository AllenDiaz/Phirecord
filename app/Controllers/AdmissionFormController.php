<?php

declare(strict_types = 1);

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Slim\Views\Twig;
use App\Entity\Patient;
use App\Enum\NavHospital;
use App\ResponseFormatter;
use App\Entity\AdmissionForm;
use App\Services\DoctorService;
use App\Entity\RequestAdmission;
use App\Services\RequestService;
use App\Services\HospitalService;
use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\DataObjects\AdmissionFormData;
use App\Services\AdmissionFormService;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\SubmitAdmissionFormRequestValidator;

class AdmissionFormController 
{

    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalService $hospitalService,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly AdmissionFormService $admissionFormService,
        private readonly DoctorService $doctorService,
        private readonly Twig $twig,
    )
    {

    }

    public function submitForm(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(SubmitAdmissionFormRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->admissionFormService->submitForm(new AdmissionFormData(new \DateTime($data['admissionDate']), 
        $data['patient'], $data['doctor'], $data['familyMember'], $data['symptoms'], $data['bloodPressure'], $data['temperature'], 
        $data['weight'], $data['respiratoryRate'], $data['pulseRate'], $data['oxygenSaturation'],
        $data['diagnosis']
    )
    );

     return $response;

    }
    public function update(Request $request, Response $response, AdmissionForm $admissionForm): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($admissionForm->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        
        $data = $this->requestValidatorFactory->make(SubmitAdmissionFormRequestValidator::class)->validate(
            $request->getParsedBody()
        );

       $this->entityManagerService->sync($this->admissionFormService->updateForm($admissionForm, new AdmissionFormData(new \DateTime($data['admissionDate']), 
        $data['patient'], $data['doctor'], $data['familyMember'], $data['symptoms'], $data['bloodPressure'], $data['temperature'], 
        $data['weight'], $data['respiratoryRate'], $data['pulseRate'], $data['oxygenSaturation'],
        $data['diagnosis'])
    ));

     return $response;

    }

    public function getAdmissionPatient(Request $request, Response $response, Patient $patient): Response
    {       
            if($this->session->get('patientSearch')){
                $this->session->forget('patientSearch');
            }
             $this->session->put('patientSearch', $patient->getId());

             $patientName = $patient->getName();

              return $this->twig->render($response, 'admission_form/patient_admission.twig',  ['isActive' => [
            'entity' => NavHospital::DOCTOR, 'entityPatient' => TRUE], 'patientName' => $patientName, 'hospitalPatient' => TRUE, 'doctors' => $this->doctorService->getDoctorNames(), ]);

        }

    public function getAdmission(Request $request, Response $response, AdmissionForm $admissionForm): Response
    {
        $hospital = $request->getAttribute('hospital');
        if($admissionForm->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
            $currentDate = new \DateTime;
            $age = $currentDate->diff($admissionForm->getPatient()->getBirthdate())->y;
            $data = [
            'id'                 =>  $admissionForm->getId(),
            'hospitalName'       => $admissionForm->getHospital()->getName(),
            'patientName'       => $admissionForm->getPatient()->getName(),
            'patientGender'       => $admissionForm->getPatient()->getGender(),
            'patientAddress'       => $admissionForm->getPatient()->getAddress(),
            'patientAge'        => $age,
            'patient'            =>  $admissionForm->getPatient()->getId(),
            'admissionDate'      => $admissionForm->getAdmissionDate()->format('Y-m-d'),
            'doctor'             => $admissionForm->getDoctor()->getId(),
            'familyMember'       => $admissionForm->getFamilyMember(),
            'symptoms'           => $admissionForm->getSymptoms(),
            'weight'             => $admissionForm->getWeight(),
            'bloodPressure'      => $admissionForm->getBloodPressure(),
            'temperature'        =>   $admissionForm->getTemperature(),
            'respiratoryRate'      => $admissionForm->getRespiratoryRate(),
            'pulseRate'      => $admissionForm->getPulseRate(),
            'oxygenSaturation'      => $admissionForm->getOxygenSaturation(),
            'diagnosis'      => $admissionForm->getDiagnosis(),
        ];

        return $this->responseFormatter->asJson($response, $data);
    }
    public function admissionRequestView(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'admission_form/admission_request.twig',  ['isActive' => [
        'patientRequest' => TRUE, 'requestAdmission' => TRUE],
    
        ]);

    }

    public function getAdmissionReferred(Request $request, Response $response, Patient $patient): Response
    {       
            if($this->session->get('patientSearch')){
                $this->session->forget('patientSearch');
            }
             $this->session->put('patientSearch', $patient->getId());

             $patientName = $patient->getName();

              return $this->twig->render($response, 'admission_form/patient_admission.twig',  ['isActive' => [
            'referral' => TRUE, 'referralAccepted' => TRUE], 'patientName' => $patientName, 'hospitalPatient' => FALSE]);

        }

        public function showAdmissionLoad(Request $request, Response $response): Response
        {
            
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $admissionForms  = $this->admissionFormService->getPaginatedAdmission($params);
            $transformer = function (AdmissionForm $admissionForm) {
            $ownRecord = FALSE;
            $hospitalId = $this->session->get('hospital');
            if($admissionForm->getHospital()->getId() === $hospitalId) {
                $ownRecord = TRUE;
            }
            return [
                    'id'   => $admissionForm->getId(),
                    'patient' => $admissionForm->getPatient()->getName(),
                    'admissionDate' => $admissionForm->getAdmissionDate()->format('F j, Y'),
                    'hospital' => $admissionForm->getHospital()->getName(),
                    'doctor' => $admissionForm->getDoctor()->getName(),
                    'ownRecord' => $ownRecord,

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
            $admissionForms  = $this->admissionFormService->getPaginatedHospitalAdmission($params);
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
                    'requestId'      => $admissionForm->getRequestAdmission()->getId(),

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
    public function delete(Request $request, Response $response,  AdmissionForm $admissionForm): Response 
    {
        $hospital = $request->getAttribute('hospital');
        if($admissionForm->getHospital()->getId() !== $hospital->getId()) {
            return $response->withStatus(401);
        }
        $this->entityManagerService->delete($admissionForm, true);

        return $response;
    }   

    public function admisionRequestCompleted(Request $request, Response $response, AdmissionForm $admissionForm, RequestAdmission $requestAdmission): Response
    {
        if ($requestAdmission->getAdmissionForm()->getId() !== $admissionForm->getId()) {
            return $response->withStatus(401);
        }

        $admissionForm->setRequested(FALSE);
        $this->entityManagerService->sync($admissionForm);
        $this->entityManagerService->delete($requestAdmission, true);


        return $response;
    }
    
    public function admissionPdf(Request $request, Response $response, AdmissionForm $admissionForm): Response
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
            .watermark {
                position: fixed;
                top: 45%;
                left: 35%;
                transform: rotate(-45deg);
                color: lightgray;
                font-size: 130px;
                opacity: 0.2;
            }
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
            <div class="watermark"></div>
            <div class="container">
                <div class="header">
                    <h1>Admission Form Record</h1>
                </div>
                <div class="hospital-info">
                    <p>' . $admissionForm->getHospital()->getName(). '</p>
                    <p>' . $admissionForm->getHospital()->getAddress(). '</p>
                    <p>Contact Number: ' . $admissionForm->getHospital()->getContactNumber(). '</p>
                </div>
                <div class="content">
                    <!-- Your medical record content goes here -->
                    <p><b>Patient Name: </b>' . $admissionForm->getPatient()->getName(). '</p>
                    <p><b>Patient Doctor: </b> Dr ' . $admissionForm->getDoctor()->getName(). '</p>
                    <p><b>Date of Admission: </b>' . $admissionForm->getAdmissionDate()->format('m/d/Y') . '</p>
                    <p> <b>Date of Birth: </b>' . $admissionForm->getPatient()->getBirthdate()->format('m/d/Y') . '</p>
                    <p><b>Family Member: </b>'. $admissionForm->getFamilyMember() .'</p>
                    <p><b>Symptoms: </b>'. $admissionForm->getSymptoms() .'</p>
                    <p><b>Blood Pressure: </b> '. $admissionForm->getBloodPressure() .'</p>
                    <p><b>Temperature: </b>'. $admissionForm->getTemperature() .'</p>
                    <p><b>Weight: </b>'. $admissionForm->getWeight() .'</p>
                    <p><b>Respiratory Rate: </b>'. $admissionForm->getRespiratoryRate() .'</p>
                    <p><b>Pulse Rate: </b>'. $admissionForm->getPulseRate() .'</p>
                    <p><b>Oxygen Saturation: </b>'. $admissionForm->getOxygenSaturation() .'</p>
                    <p><b>Diagnosis: </b>'. $admissionForm->getDiagnosis() .'</p>
                </div>
            </div>
            <div class="footer">
                <p>
This document is legal if it has a signature or stamp from the hospital or doctor.</p>
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
            $text =  $admissionForm->getPatient()->getName();
            $fullName = explode(' ', $text);
            $firstName = trim($fullName[0]); 
            $dompdf->stream(''.$firstName.'AdmissionRecord.pdf', array('Attachment' => 0));

            return $response;
            
        }
    public function patientAdmissionPdf(Request $request, Response $response, AdmissionForm $admissionForm): Response
    {
        $patient = $request->getAttribute('patient');
        if ($admissionForm->getPatient()->getId() !== $patient->getId()) {
            return $response->withStatus(401);
        }
        
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
            .watermark {
                position: fixed;
                top: 20%;
                left: 20%;
                transform: rotate(-45deg);
                color: lightgray;
                font-size: 130px;
                opacity: 0.2;
            }
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
            <div class="watermark">NOT OFFICIAL COPY</div>
            <div class="container">
                <div class="header">
                    <h1>Admission Form Record</h1>
                </div>
                <div class="hospital-info">
                    <p>' . $admissionForm->getHospital()->getName(). '</p>
                    <p>' . $admissionForm->getHospital()->getAddress(). '</p>
                    <p>Contact Number: ' . $admissionForm->getHospital()->getContactNumber(). '</p>
                </div>
                <div class="content">
                    <!-- Your medical record content goes here -->
                    <p><b>Patient Name: </b>' . $admissionForm->getPatient()->getName(). '</p>
                    <p><b>Patient Doctor: </b> Dr ' . $admissionForm->getDoctor()->getName(). '</p>
                    <p><b>Date of Admission: </b>' . $admissionForm->getAdmissionDate()->format('m/d/Y') . '</p>
                    <p> <b>Date of Birth: </b>' . $admissionForm->getPatient()->getBirthdate()->format('m/d/Y') . '</p>
                    <p><b>Family Member: </b>'. $admissionForm->getFamilyMember() .'</p>
                    <p><b>Symptoms: </b>'. $admissionForm->getSymptoms() .'</p>
                    <p><b>Blood Pressure: </b> '. $admissionForm->getBloodPressure() .'</p>
                    <p><b>Temperature: </b>'. $admissionForm->getTemperature() .'</p>
                    <p><b>Weight: </b>'. $admissionForm->getWeight() .'</p>
                    <p><b>Respiratory Rate: </b>'. $admissionForm->getRespiratoryRate() .'</p>
                    <p><b>Pulse Rate: </b>'. $admissionForm->getPulseRate() .'</p>
                    <p><b>Oxygen Saturation: </b>'. $admissionForm->getOxygenSaturation() .'</p>
                    <p><b>Diagnosis: </b>'. $admissionForm->getDiagnosis() .'</p>
                </div>
            </div>
            <div class="footer">
                <p>This document is not official, You cannot use it for legal purposes unless there is a signature or seal from the assigned doctor or health facility.</p>
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
            $text =  $admissionForm->getPatient()->getName();
            $fullName = explode(' ', $text);
            $firstName = trim($fullName[0]); 
            $dompdf->stream(''.$firstName.'AdmissionRecord.pdf', array('Attachment' => 0));

            return $response;
            
        }

    
}