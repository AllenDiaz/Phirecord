<?php

declare(strict_types = 1);

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Slim\Views\Twig;
use App\Entity\Patient;
use App\Enum\NavHospital;
use App\ResponseFormatter;
use App\Entity\RequestMedical;
use App\Services\DoctorService;
use App\Services\RequestService;
use App\Services\HospitalService;
use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\DataObjects\MedicalCertificateData;
use App\Services\MedicalCertificateService;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\SubmitMedicalRequestValidator;
use App\RequestValidators\SubmitCheckupFormRequestValidator;

class MedicalCertificateController 
{

    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalService $hospitalService,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly MedicalCertificateService $medicalCertificateService,
        private readonly DoctorService $doctorService,
        private readonly Twig $twig,
    )
    {

    }

    public function submitForm(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(SubmitMedicalRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->medicalCertificateService->submitForm(new MedicalCertificateData(new \DateTime($data['certificateDate']), $data['patient'], $data['doctor'], $data['impression'], $data['purpose']));

     return $response;

    }
    public function update(Request $request, Response $response, MedicalCertificate $medicalCertificate): Response
    {
        $data = $this->requestValidatorFactory->make(SubmitMedicalRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->entityManagerService->sync($this->medicalCertificateService->updateForm($medicalCertificate, new MedicalCertificateData(new \DateTime($data['certificateDate']), $data['patient'], $data['doctor'], $data['impression'], $data['purpose'])));

     return $response;

    }
    public function delete(Request $request, Response $response, MedicalCertificate $medicalCertificate): Response 
    {
        $this->entityManagerService->delete($medicalCertificate, true);
         return $response;
    }
    public function getMedicalPatient(Request $request, Response $response, Patient $patient): Response
    {       
        if($this->session->get('patientSearch')){
            $this->session->forget('patientSearch');
        }
            $this->session->put('patientSearch', $patient->getId());

            $patientName = $patient->getName();

            return $this->twig->render($response, 'medical_form/patient_medical.twig',  ['isActive' => [
                    'entity' => NavHospital::DOCTOR, 'entityPatient' => TRUE], 'patientName' => $patientName, 'hospitalPatient' => TRUE,
                    'doctors' => $this->doctorService->getDoctorNames()
                ]);

    }

    public function getMedicalReferred(Request $request, Response $response, Patient $patient): Response
    {       
            if($this->session->get('patientSearch')){
                $this->session->forget('patientSearch');
            }
             $this->session->put('patientSearch', $patient->getId());

             $patientName = $patient->getName();

              return $this->twig->render($response, 'medical_form/patient_medical.twig',  ['isActive' => [
            'referral' => TRUE, 'referralAccepted' => TRUE], 'patientName' => $patientName, 'hospitalPatient' => FALSE]);

    }
    public function medicalRequestView(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'medical_form/medical_request.twig',  ['isActive' => [
        'patientRequest' => TRUE, 'requestMedical' => TRUE], ]);

    }

        public function showMedicalLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $medicalCertificates  = $this->medicalCertificateService->getPaginatedMedical($params);
            $transformer = function (MedicalCertificate $medicalCertificate) {
            $ownRecord = FALSE;
            $hospitalId = $this->session->get('hospital');
            if($medicalCertificate->getHospital()->getId() === $hospitalId) {
                $ownRecord = TRUE;
            }
            return [
                    'id'   => $medicalCertificate->getId(),
                    'patient' => $medicalCertificate->getPatient()->getName(),
                    'certificateDate' => $medicalCertificate->getCertificateDate()->format('m/d/Y g:i A'),
                    'hospital' => $medicalCertificate->getHospital()->getName(),
                    'doctor' => $medicalCertificate->getDoctor()->getName(),
                    'ownRecord' => $ownRecord,

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
        public function getMedical(Request $request, Response $response, MedicalCertificate $medicalCertificate): Response
    {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($medicalCertificate->getPatient()->getBirthdate())->y;
            $data = [
            'id'                 =>  $medicalCertificate->getId(),
            'hospitalName'       => $medicalCertificate->getHospital()->getName(),
            'patientName'       => $medicalCertificate->getPatient()->getName(),
            'patientGender'       => $medicalCertificate->getPatient()->getGender(),
            'patientAddress'       => $medicalCertificate->getPatient()->getAddress(),
            'patientAge'        => $age,
            'patient'            =>  $medicalCertificate->getPatient()->getId(),
            'doctor'             => $medicalCertificate->getDoctor()->getId(),
            'certificateDate'      => $medicalCertificate->getCertificateDate()->format('Y-m-d'),
            'impression'      => $medicalCertificate->getImpression(),
            'purpose'      => $medicalCertificate->getPurpose(),
    
        ];

        return $this->responseFormatter->asJson($response, $data);
    }

        public function medicalRequestLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $medicalCertificates  = $this->medicalCertificateService->getPaginatHospitalRequestMedical($params);
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
                    'requestId'      => $medicalCertificate->getRequestMedical()->getId(),
                    

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

    public function medicalRequestCompleted(Request $request, Response $response, MedicalCertificate $medicalCertificate, RequestMedical $requestMedical): Response
    {
        if ($requestMedical->getMedicalCertificate()->getId() !== $medicalCertificate->getId()) {
            return $response->withStatus(401);
        }
        $medicalCertificate->setRequested(FALSE);
        $this->entityManagerService->sync($medicalCertificate);
        $this->entityManagerService->delete($medicalCertificate->getRequestMedical(), true);


        return $response;
    }

    public function medicalPdf(Request $request, Response $response, MedicalCertificate $medicalCertificate): Response
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
                    <h1>Medical Certificate</h1>
                </div>
                <div class="hospital-info">
                    <p>' . $medicalCertificate->getHospital()->getName(). '</p>
                    <p>' . $medicalCertificate->getHospital()->getAddress(). '</p>
                    <p>Contact Number: ' . $medicalCertificate->getHospital()->getContactNumber(). '</p>
                </div>
                <div class="content">
                    <!-- Your medical record content goes here -->
                            <!-- Your medical certificate content goes here -->
                            <p>This is to certify that:</p>
                            <p><strong>Patient Name:</strong> '. $medicalCertificate->getPatient()->getName() .' </p>
                            <p><strong>Impression:</strong> </p>
                            <p>Based on my examination and assessment, the patient is:</p>
                            <p> '. $medicalCertificate->getImpression() .' </p>
                            <p><strong>Purpose</p>
                            <p>for '. $medicalCertificate->getPurpose() .'</p>
                            <p>Issued on ' . $medicalCertificate->getCertificateDate()->format('m/d/Y') . '</p>
                            <br>
                            <p><strong>Doctor Name:</strong> Dr '. $medicalCertificate->getDoctor()->getName() .' </p>
                            
                </div>
            </div>
            <div class="footer">
                <p>This document is legal if it has a signature or stamp from the hospital or doctor.</p>
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
            $text =  $medicalCertificate->getPatient()->getName();
            $fullName = explode(' ', $text);
            $firstName = trim($fullName[0]); 
            $dompdf->stream(''.$firstName.'MedicalCertificate.pdf', array('Attachment' => 0));

            return $response;
            
        }

    public function patientMedicalPdf(Request $request, Response $response, MedicalCertificate $medicalCertificate): Response
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
                    <h1>Medical Certificate</h1>
                </div>
                <div class="hospital-info">
                    <p>' . $medicalCertificate->getHospital()->getName(). '</p>
                    <p>' . $medicalCertificate->getHospital()->getAddress(). '</p>
                    <p>Contact Number: ' . $medicalCertificate->getHospital()->getContactNumber(). '</p>
                </div>
                <div class="content">
                    <!-- Your medical record content goes here -->
                            <!-- Your medical certificate content goes here -->
                            <p>This is to certify that:</p>
                            <p><strong>Patient Name:</strong> '. $medicalCertificate->getPatient()->getName() .' </p>
                            <p><strong>Impression:</strong> </p>
                            <p>Based on my examination and assessment, the patient is:</p>
                            <p> '. $medicalCertificate->getImpression() .' </p>
                            <p><strong>Purpose</p>
                            <p>for '. $medicalCertificate->getPurpose() .'</p>
                            <p>Issued on ' . $medicalCertificate->getCertificateDate()->format('m/d/Y') . '</p>
                            <br>
                            <p><strong>Doctor Name:</strong> Dr '. $medicalCertificate->getDoctor()->getName() .' </p>
                            
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
            $text =  $medicalCertificate->getPatient()->getName();
            $fullName = explode(' ', $text);
            $firstName = trim($fullName[0]); 
            $dompdf->stream(''.$firstName.'MedicalCertificate.pdf', array('Attachment' => 0));

            return $response;
            
        }


}