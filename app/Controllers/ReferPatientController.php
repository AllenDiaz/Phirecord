<?php

declare(strict_types = 1);

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Slim\Views\Twig;
use App\Entity\Patient;
use App\Entity\Referral;
use App\Enum\NavHospital;
use App\ResponseFormatter;
use App\Services\DoctorService;
use App\Services\RequestService;
use App\Services\HospitalService;
use App\Entity\MedicalCertificate;
use App\Contracts\SessionInterface;
use App\DataObjects\ReferPatientData;
use App\Services\ReferPatientService;
use App\DataObjects\MedicalCertificateData;
use App\Services\MedicalCertificateService;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\HospitalProviderServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\RequestValidators\SubmitMedicalRequestValidator;
use App\RequestValidators\SubmitCheckupFormRequestValidator;
use App\RequestValidators\SubmitReferPatientRequestValidator;

class ReferPatientController 
{

    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly EntityManagerServiceInterface $entityManagerService,
        private readonly SessionInterface $session,
        private readonly HospitalService $hospitalService,
        private readonly HospitalProviderServiceInterface $hospitalProviderService,
        private readonly RequestService $requestService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly ReferPatientService $referPatientService,
        private readonly DoctorService $doctorService,
        private readonly Twig $twig,
    )
    {

    }

    public function submitForm(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(SubmitReferPatientRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->referPatientService->submitForm(new ReferPatientData($data['patient'], (int)$data['referHospital']));

     return $response;

    }
    public function getReferralRequest(Request $request, Response $response): Response
    {    
        
        return $this->twig->render($response, 'referral/patient_referral.twig',  ['isActive' => [
            'referral' => TRUE, 'referralPending' => TRUE]]);
    }
    public function getReferredRequest(Request $request, Response $response): Response
    {    
        
        return $this->twig->render($response, 'referral/patient_referred.twig',  ['isActive' => [
            'referral' => TRUE, 'referralAccepted' => TRUE], 'doctors' => $this->doctorService->getDoctorNames()]);
    }
    public function referralHospital(Request $request, Response $response): Response
    {    
        
        return $this->twig->render($response, 'referral/patient_hospital_referral.twig',  ['isActive' => [
            'referral' => TRUE, 'referralHospital' => TRUE], ]);
    }

    public function pendingLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $referrals  = $this->referPatientService->getPaginatedPending($params);
            $transformer = function (Referral $referral) {

            
            return [
                    'id'   => $referral->getId(),
                    'patient' => $referral->getPatient()->getName(),
                    'createdAt' => $referral->getCreatedAt()->format('F j, Y g:i A'),
                    'hospital' => $referral->getHospital()->getName(),
                    'referralCode' => $referral->getReferralCode()

                ];
            };

            $totalReferral = count($referrals);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $referrals->getIterator()),
                $params->draw,
                $totalReferral
            );
    }

    public function acceptedLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $referrals  = $this->referPatientService->getPaginatedAccepted($params);
            $transformer = function (Referral $referral) {

            
            return [
                    'id'   => $referral->getId(),
                    'patient' => $referral->getPatient()->getName(),
                    'patientId' => $referral->getPatient()->getId(),
                    'patientName' => $referral->getPatient()->getName(),
                    'patientAddress' => $referral->getPatient()->getAddress(),
                    'patientGender' => $referral->getPatient()->getGender(),
                    'createdAt' => $referral->getCreatedAt()->format('F j, Y g:i A'),
                    'hospital' => $referral->getHospital()->getName(),
                    'referralCode' => $referral->getReferralCode()

                ];
            };

            $totalReferral = count($referrals);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $referrals->getIterator()),
                $params->draw,
                $totalReferral
            );
    }
    public function hospitalReferralLoad(Request $request, Response $response): Response
        {
            $params      = $this->requestService->getDataTableQueryParameters($request);
            $referrals  = $this->referPatientService->getPaginatedHospitalReffer($params);
            $transformer = function (Referral $referral) {

            
            return [
                    'id'   => $referral->getId(),
                    'patient' => $referral->getPatient()->getName(),
                    'patientId' => $referral->getPatient()->getId(),
                    'patientName' => $referral->getPatient()->getName(),
                    'patientAddress' => $referral->getPatient()->getAddress(),
                    'patientGender' => $referral->getPatient()->getGender(),
                    'createdAt' => $referral->getCreatedAt()->format('F j, Y g:i A'),
                    'hospital' => $referral->getHospital()->getName(),
                    'referralCode' => $referral->getReferralCode(),
                    'status' => $referral->getIsAccepted() ? 'Accepted' : 'Pending'

                ];
            };

            $totalReferral = count($referrals);

            return $this->responseFormatter->asDataTable(
                $response,
                array_map($transformer, (array) $referrals->getIterator()),
                $params->draw,
                $totalReferral
            );
    }

    public function referralPdf(Request $request, Response $response, Referral $referral): Response
    {
         // Create an instance of Dompdf with options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $hospital = $this->hospitalProviderService->getById($referral->getToHospital());
        

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
                    <h1>Patient Referral Form</h1>
                </div>
                <div class="hospital-info">
                    <p>'.$referral->getHospital()->getName() .'</p>
                    <p> '.$referral->getHospital()->getAddress() .'</p>
                    <p> '.$referral->getHospital()->getContactNumber() .' </p>
                </div>
                <div class="content container">
                    <!-- Your medical record content goes here -->
                    <p>Please provide the reference number to '. $referral->getHospital()->getName() .' Health Facility for the health support</p>
                    <p><b>Reference Number:</b> '. $referral->getReferralCode() .'</p>
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
            $dompdf->stream('Referral'. $referral->getHospital()->getName() .'.pdf', array('Attachment' => 0));
    }
    public function rejectReferral(Request $request, Response $response, Referral $referral): Response
    {
        $this->entityManagerService->delete($referral, true);

        return $response;
    }
    public function acceptReferral(Request $request, Response $response, Referral $referral): Response
    {
        $this->entityManagerService->sync($this->referPatientService->approveReferral($referral));

        return $response;
    }

}