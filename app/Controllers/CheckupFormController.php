<?php

declare(strict_types = 1);

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Slim\Views\Twig;
use App\Entity\Patient;
use App\Enum\NavHospital;
use App\ResponseFormatter;
use App\Entity\RequestCheckup;
use App\Entity\PrenatalCheckup;
use App\Services\DoctorService;
use App\Services\RequestService;
use App\Services\HospitalService;
use App\Contracts\SessionInterface;
use App\DataObjects\CheckupFormData;
use App\Services\CheckupFormService;
use App\DataObjects\AdmissionFormData;
use App\Services\AdmissionFormService;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\SubmitCheckupFormRequestValidator;

class CheckupFormController 
{

    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalService $hospitalService,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly CheckupFormService $checkupFormService,
        private readonly DoctorService $doctorService,
        private readonly Twig $twig,
    )
    {

    }

    public function submitForm(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(SubmitCheckupFormRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->checkupFormService->submitForm(new CheckupFormData
        (new \DateTime($data['confineDate']), new \DateTime($data['checkupDate']), $data['patient'], $data['doctor'], $data['familyMember'],
        new \DateTime($data['menstrualDate']),  $data['fetalHeartTones'], $data['gravida'], $data['para'], 
        $data['labaratory'], $data['urinalysis'], $data['bloodCount'], $data['fecalysis']
    )
    );
    
     return $response;
    }

    public function update(Request $request, Response $response, PrenatalCheckup $prenatalCheckup): Response
    {
        $data = $this->requestValidatorFactory->make(SubmitCheckupFormRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->entityManagerService->sync($this->checkupFormService->updateForm($prenatalCheckup, new CheckupFormData
        (new \DateTime($data['confineDate']), new \DateTime($data['checkupDate']), $data['patient'], $data['doctor'], $data['familyMember'],
        new \DateTime($data['menstrualDate']),  $data['fetalHeartTones'], $data['gravida'], $data['para'], 
        $data['labaratory'], $data['urinalysis'], $data['bloodCount'], $data['fecalysis']
    )
    ));
    
     return $response;
    }

    public function delete(Request $request, Response $response,  PrenatalCheckup $prenatalCheckup): Response 
    {
        $this->entityManagerService->delete($prenatalCheckup, true);

        return $response;
    }   

    public function getCheckupPatient(Request $request, Response $response, Patient $patient): Response
    {       
            if($this->session->get('patientSearch')){
                $this->session->forget('patientSearch');
            }
             $this->session->put('patientSearch', $patient->getId());

             $patientName = $patient->getName();

            return $this->twig->render($response, 'checkup_form/patient_checkup.twig',  ['isActive' => [
            'entity' => NavHospital::DOCTOR, 'entityPatient' => TRUE], 'patientName' => $patientName, 'hospitalPatient' => TRUE, 'doctors' => $this->doctorService->getDoctorNames()]);

    }

    public function getCheckupReferred(Request $request, Response $response, Patient $patient): Response
    {       
            if($this->session->get('patientSearch')){
                $this->session->forget('patientSearch');
            }
             $this->session->put('patientSearch', $patient->getId());

             $patientName = $patient->getName();

              return $this->twig->render($response, 'checkup_form/patient_checkup.twig',  ['isActive' => [
            'referral' => TRUE, 'referralAccepted' => TRUE], 'patientName' => $patientName, 'hospitalPatient' => FALSE]);

        }

        public function showCheckupLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $checkups  = $this->checkupFormService->getPaginatedCheckup($params);
            $transformer = function (PrenatalCheckup $checkup) {
            $ownRecord = FALSE;
            $hospitalId = $this->session->get('hospital');
            if($checkup->getHospital()->getId() === $hospitalId) {
                $ownRecord = TRUE;
            }
            return [
                    'id'   => $checkup->getId(),
                    'ownRecord' => $ownRecord,
                    'patient' => $checkup->getPatient()->getName(),
                    'checkupDate' => $checkup->getCheckupDate()->format('m/d/Y g:i A'),
                    'hospital' => $checkup->getHospital()->getName(),
                    'doctor' => $checkup->getDoctor()->getName()

                ];
            };

            $totalcheckups = count($checkups);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $checkups->getIterator()),
                $params->draw,
                $totalcheckups
            );
    }
     public function getCheckup(Request $request, Response $response, PrenatalCheckup $prenatalCheckup): Response
    {
            $currentDate = new \DateTime;
            $age = $currentDate->diff($prenatalCheckup->getPatient()->getBirthdate())->y;
            $data = [
            'id'                 =>  $prenatalCheckup->getId(),
            'hospitalName'       => $prenatalCheckup->getHospital()->getName(),
            'patientName'       => $prenatalCheckup->getPatient()->getName(),
            'patientGender'       => $prenatalCheckup->getPatient()->getGender(),
            'patientAddress'       => $prenatalCheckup->getPatient()->getAddress(),
            'patientAge'        => $age,
            'patient'            =>  $prenatalCheckup->getPatient()->getId(),
            'confineDate'      => $prenatalCheckup->getConfineDateEstimated()->format('Y-m-d'),
            'checkupDate'      => $prenatalCheckup->getCheckupDate()->format('Y-m-d'),
            'doctor'      => $prenatalCheckup->getDoctor()->getId(),
            'familyMember'      => $prenatalCheckup->getFamilyMember(),
            'patient'      => $prenatalCheckup->getPatient()->getId(),
            'menstrualDate'      => $prenatalCheckup->getLastMenstrualDate()->format('Y-m-d'),
            'fetalHeartTones'      => $prenatalCheckup->getFetalHeartTones(),
            'gravida'                => $prenatalCheckup->getGravida(),
            'para'                => $prenatalCheckup->getPara(),
            'labaratory'                => $prenatalCheckup->getLabaratory(),
            'urinalysis'                => $prenatalCheckup->getUrinalysis(),
            'bloodCount'                => $prenatalCheckup->getBloodCount(),
            'fecalysis'                => $prenatalCheckup->getFecalysis(),
            
          
        ];

        return $this->responseFormatter->asJson($response, $data);
    }
    public function checkupRequestView(Request $request, Response $response): Response
    {       

        return $this->twig->render($response, 'checkup_form/checkup_request.twig',  ['isActive' => [
        'patientRequest' => TRUE, 'requestCheckup' => TRUE], ]);

    }
        public function checkupRequestLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $prenatalCheckups  = $this->checkupFormService->getPaginatedRequestHospitalCheckup($params);
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
                    'requestId'      => $prenatalCheckup->getRequestCheckup()->getId(),
   
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

    public function checkupRequestCompleted(Request $request, Response $response, PrenatalCheckup $prenatalCheckup, RequestCheckup $requestCheckup): Response
    {
        if ($requestCheckup->getPrenatalCheckup()->getId() !== $prenatalCheckup->getId()) {
            return $response->withStatus(401);
        }
        
        $prenatalCheckup->setRequested(FALSE);
        $this->entityManagerService->sync($prenatalCheckup);
        $this->entityManagerService->delete($requestCheckup, true);


        return $response;
    }
    

     public function checkupPdf(Request $request, Response $response, PrenatalCheckup $prenatalCheckup): Response
    {
        
        // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $urinalysis = $prenatalCheckup->getUrinalysis() ? $prenatalCheckup->getUrinalysis() : "N/A";
        $heartStone = $prenatalCheckup->getFetalHeartTones() ? $prenatalCheckup->getFetalHeartTones() : "N/A";
        $labaratory = $prenatalCheckup->getLabaratory() ? $prenatalCheckup->getLabaratory() : "N/A";
        $bloodCount = $prenatalCheckup->getBloodCount() ? $prenatalCheckup->getBloodCount() : "N/A";
        $fecalysis = $prenatalCheckup->getFecalysis() ? $prenatalCheckup->getFecalysis() : "N/A";
        $gravida = $prenatalCheckup->getGravida() ? $prenatalCheckup->getGravida()  : "N/A";
        $para = $prenatalCheckup->getPara() ? $prenatalCheckup->getPara()  : "N/A";


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
                    <h1>Checkup Record</h1>
                </div>
                <div class="hospital-info">
                    <p>' . $prenatalCheckup->getHospital()->getName(). '</p>
                    <p>' . $prenatalCheckup->getHospital()->getAddress(). '</p>
                    <p>Contact Number: ' . $prenatalCheckup->getHospital()->getContactNumber(). '</p>
                </div>
                <div class="content">
                    <!-- Your medical record content goes here -->
                    <p><b>Patient Name: </b>' . $prenatalCheckup->getPatient()->getName(). '</p>
                    <p><b>Patient Doctor: </b> Dr ' . $prenatalCheckup->getDoctor()->getName(). '</p>
                    <p><b>Checkup Issued: </b>' . $prenatalCheckup->getCheckupDate()->format('m/d/Y') . '</p>
                    <p> <b>Date of Birth: </b>' . $prenatalCheckup->getPatient()->getBirthdate()->format('m/d/Y') . '</p>
                    <p><b>Family Member: </b>'. $prenatalCheckup->getFamilyMember() .'</p>
                    <p><b>Last Menstrual: </b>'. $prenatalCheckup->getLastMenstrualDate()->format('m/d/Y') .'</p>
                    <p><b>Fetal HeartTones: </b> '. $heartStone .'</p>
                    <p><b>Gravida: </b>'. $gravida .'</p>
                    <p><b>Para: </b>'. $para .'</p>
                    <p><b>Labaratory Notes: </b></p>
                    <p> - '. $labaratory .' </p>
                     <p><b>Urinalysis Notes: </b></p>
                    <p> - '.  $urinalysis .'</p>
                     <p><b>Oxygen Saturation Notes: </b></p>
                    <p> - '. $bloodCount .'</p>
                    <p><b>Diagnosis Notes:  </b></p>
                    <p> - '. $fecalysis .'</p>
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
            $text =  $prenatalCheckup->getPatient()->getName();
            $fullName = explode(' ', $text);
            $firstName = trim($fullName[0]); 
            $dompdf->stream(''.$firstName.'chekup.pdf', array('Attachment' => 0));

            return $response;
            
        }

     public function patientCheckupPdf(Request $request, Response $response, PrenatalCheckup $prenatalCheckup): Response


    {
        $patient = $request->getAttribute('patient');
        if ($prenatalCheckup->getPatient()->getId() !== $patient->getId()) {
            return $response->withStatus(401);
        }
        // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $urinalysis = $prenatalCheckup->getUrinalysis() ? $prenatalCheckup->getUrinalysis() : "N/A";
        $heartStone = $prenatalCheckup->getFetalHeartTones() ? $prenatalCheckup->getFetalHeartTones() : "N/A";
        $labaratory = $prenatalCheckup->getLabaratory() ? $prenatalCheckup->getLabaratory() : "N/A";
        $bloodCount = $prenatalCheckup->getBloodCount() ? $prenatalCheckup->getBloodCount() : "N/A";
        $fecalysis = $prenatalCheckup->getFecalysis() ? $prenatalCheckup->getFecalysis() : "N/A";
        $gravida = $prenatalCheckup->getGravida() ? $prenatalCheckup->getGravida()  : "N/A";
        $para = $prenatalCheckup->getPara() ? $prenatalCheckup->getPara()  : "N/A";


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
                    <h1>Checkup Record</h1>
                </div>
                <div class="hospital-info">
                    <p>' . $prenatalCheckup->getHospital()->getName(). '</p>
                    <p>' . $prenatalCheckup->getHospital()->getAddress(). '</p>
                    <p>Contact Number: ' . $prenatalCheckup->getHospital()->getContactNumber(). '</p>
                </div>
                <div class="content">
                    <!-- Your medical record content goes here -->
                    <p><b>Patient Name: </b>' . $prenatalCheckup->getPatient()->getName(). '</p>
                    <p><b>Patient Doctor: </b> Dr ' . $prenatalCheckup->getDoctor()->getName(). '</p>
                    <p><b>Checkup Issued: </b>' . $prenatalCheckup->getCheckupDate()->format('m/d/Y') . '</p>
                    <p> <b>Date of Birth: </b>' . $prenatalCheckup->getPatient()->getBirthdate()->format('m/d/Y') . '</p>
                    <p><b>Family Member: </b>'. $prenatalCheckup->getFamilyMember() .'</p>
                    <p><b>Last Menstrual: </b>'. $prenatalCheckup->getLastMenstrualDate()->format('m/d/Y') .'</p>
                    <p><b>Fetal HeartTones: </b> '. $heartStone .'</p>
                    <p><b>Gravida: </b>'. $gravida .'</p>
                    <p><b>Para: </b>'. $para .'</p>
                    <p><b>Labaratory Notes: </b></p>
                    <p> - '. $labaratory .' </p>
                     <p><b>Urinalysis Notes: </b></p>
                    <p> - '.  $urinalysis .'</p>
                     <p><b>Oxygen Saturation Notes: </b></p>
                    <p> - '. $bloodCount .'</p>
                    <p><b>Diagnosis Notes:  </b></p>
                    <p> - '. $fecalysis .'</p>
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
            $text =  $prenatalCheckup->getPatient()->getName();
            $fullName = explode(' ', $text);
            $firstName = trim($fullName[0]); 
            $dompdf->stream(''.$firstName.'chekup.pdf', array('Attachment' => 0));

            return $response;
            
        }


}