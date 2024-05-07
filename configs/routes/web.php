<?php

declare(strict_types = 1);

use Slim\App;
use App\Middleware\AuthMiddleware;
use App\Controllers\HomeController;
use App\Middleware\AdminMiddleware;
use App\Middleware\GuestMiddleware;
use App\Controllers\AdminController;
use App\Middleware\DoctorMiddleware;
use App\Controllers\DoctorController;
use App\Middleware\PatientMiddleware;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\PatientController;
use App\Middleware\HospitalMiddleware;
use App\Controllers\HospitalController;
use App\Middleware\HeadAdminMiddleware;
use App\Controllers\AuthAdminController;
use App\Controllers\HeadAdminController;
use App\Controllers\AuthDoctorController;
use App\Controllers\AuthPatientController;
use App\Controllers\CheckupFormController;
use App\Controllers\AuthHospitalController;
use App\Controllers\ReferPatientController;
use App\Middleware\PendingDoctorMiddleware;
use App\Controllers\AdmissionFormController;
use App\Controllers\DoctorArchiveController;
use App\Controllers\DoctorPendingController;
use App\Middleware\PendingPatientMiddleware;
use App\Controllers\PatientArchiveController;
use App\Controllers\PatientPendingController;
use App\Middleware\PendingHospitalMiddleware;
use App\Controllers\HospitalArchiveController;
use App\Controllers\HospitalPendingController;
use App\Controllers\MedicalCertificateController;

return function (App $app) {

     $app->group('', function (RouteCollectorProxy $group) {
        //Admin Route
        $group->group('/admin', function (RouteCollectorProxy $admin) {
            $admin->get('', [AdminController::class, 'index']);            
            $admin->get('/profile', [AdminController::class, 'profile']);
            $admin->get('/register/hospital', [AdminController::class, 'registerFacilityView']);
            $admin->get('/registered/hospital', [AdminController::class, 'registeredHospitalView']);
            $admin->get('/approved/hospital', [AdminController::class, 'approveFacilityView']);
            $admin->get('/pending/hospital', [AdminController::class, 'pendingFacilityView']);
            $admin->get('/archived/approve/hospital', [AdminController::class, 'approvedArchiveView']);
            $admin->get('/archived/decline/hospital', [AdminController::class, 'declinedArchiveView']);
            $admin->post('/register/hospital', [AdminController::class, 'registerFacility']);
            $admin->post('/changepassword', [AdminController::class, 'changePassword']);
            $admin->get('/changepassword', [AdminController::class, 'changePasswordView']);
            $admin->get('/logout', [AuthAdminController::class, 'logout']);
        })->add(AdminMiddleware::class);

        //Admin And Head Admin Route
        $group->group('/admin', function (RouteCollectorProxy $adminLoad) {
            $adminLoad->get('/register/hospital/load', [AdminController::class, 'loadRegistered']);
            $adminLoad->get('/register/hospital/pendingload', [AdminController::class, 'loadPending']);
            $adminLoad->get('/archive/approved/load', [AdminController::class, 'loadArchiveApproved']);
            $adminLoad->get('/archive/declined/load', [AdminController::class, 'loadArchiveDeclined']);
            $adminLoad->get('/register/accept/{hospital}', [AdminController::class, 'acceptHospital']);
            $adminLoad->get('/register/archive/{hospital}', [AdminController::class, 'approvedArchiveHospital']);
            $adminLoad->get('/declined/archive/{hospital}', [AdminController::class, 'declinedArchiveHospital']);
            $adminLoad->get('/recover/declined/{hospital}', [AdminController::class, 'recoverDeclinedHospital']);
            $adminLoad->get('/recover/approved/{hospital}', [AdminController::class, 'recoverApprovedHospital']);
            $adminLoad->delete('/registered/archive/{hospital}', [AdminController::class, 'deleteRegisterHospital']);
            $adminLoad->delete('/declined/archive/{hospital}', [AdminController::class, 'deleteDeclinedHospital']);
        });

        $group->group('/admin/head', function (RouteCollectorProxy $adminHead) {
           $adminHead->get('', [HeadAdminController::class, 'index']);
            $adminHead->get('/profile', [HeadAdminController::class, 'profile']);
            $adminHead->get('/register/hospital', [HeadAdminController::class, 'registerFacilityView']);
            $adminHead->get('/register/admin', [HeadAdminController::class, 'registerAdminView']);
            $adminHead->post('/register/admin', [HeadAdminController::class, 'registerAdmin']);
            $adminHead->get('/registered/admin', [HeadAdminController::class, 'assistantAdminView']);
            $adminHead->get('/registered/hospital', [HeadAdminController::class, 'registeredHospitalView']);
            $adminHead->get('/approved/hospital', [HeadAdminController::class, 'approveFacilityView']);
            $adminHead->get('/pending/hospital', [HeadAdminController::class, 'pendingFacilityView']);
            $adminHead->get('/archived/approve/hospital', [HeadAdminController::class, 'approvedArchiveView']);
            $adminHead->get('/archived/decline/hospital', [HeadAdminController::class, 'declinedArchiveView']);
            $adminHead->get('/register/admin/load', [HeadAdminController::class, 'loadAdmin']);
            $adminHead->post('/changepassword', [HeadAdminController::class, 'changePassword']);
            $adminHead->get('/changepassword', [HeadAdminController::class, 'changePasswordView']);
            $adminHead->post('/register/hospital', [HeadAdminController::class, 'registerFacility']);
            $adminHead->get('/logout', [AuthAdminController::class, 'logout']); 

            $adminHead->group('/assistant', function (RouteCollectorProxy $adminAssistant) {
                 $adminAssistant->delete('/delete/{admin}', [HeadAdminController::class, 'deleteAdmin']);
                 $adminAssistant->get('/assign/{admin}', [HeadAdminController::class, 'assignHead']);
            });
            })->add(HeadAdminMiddleware::class);

        // HOSPITAL ROUTE
        $group->group('/hospital', function (RouteCollectorProxy $hospital) {
           $hospital->get('', [HospitalController::class, 'index']); 
            $hospital->post('/changepassword', [HospitalController::class, 'changePassword']);
            $hospital->get('/changepassword', [HospitalController::class, 'changePasswordView']);
           $hospital->get('/logout', [AuthHospitalController::class, 'logout']);
           $hospital->get('/profile', [HospitalController::class, 'profile']);

           $hospital->group('/doctor', function(RouteCollectorProxy $hospitalDoctor) {
                $hospitalDoctor->get('/register', [HospitalController::class, 'registerDoctorView']);
                $hospitalDoctor->post('/register', [HospitalController::class, 'registerDoctor']);
                $hospitalDoctor->get('/view', [HospitalController::class, 'doctorView']);
                $hospitalDoctor->get('/accepted/archive', [HospitalController::class, 'doctorAcceptedArchiveView']);
                $hospitalDoctor->get('/declined/archive', [HospitalController::class, 'doctorDeclincedArchiveView']);
                $hospitalDoctor->get('/accepted/archive/load', [HospitalController::class, 'doctorAcceptedArchiveLoad']);
                $hospitalDoctor->get('/declined/archive/load', [HospitalController::class, 'doctorDeclinedArchiveLoad']);
                $hospitalDoctor->get('/archive/{doctor}', [HospitalController::class, 'approvedToArchiveDoctor']);
                $hospitalDoctor->get('/reject/{doctor}', [HospitalController::class, 'rejectDoctor']);
                $hospitalDoctor->get('/accept/{doctor}', [HospitalController::class, 'approveDoctor']);
                $hospitalDoctor->get('/recover/accepted/{doctor}', [HospitalController::class, 'recoverAcceptedDoctor']);
                $hospitalDoctor->delete('/accepted/archive/{doctor}', [HospitalController::class, 'deleteAcceptedDoctor']);
                $hospitalDoctor->get('/load', [HospitalController::class, 'hospitalDoctorLoad']);
                $hospitalDoctor->get('/pending', [HospitalController::class, 'doctorPendingView']);
                $hospitalDoctor->get('/pending/load', [HospitalController::class, 'doctorPendingLoad']);
           });

            $hospital->group('/patient', function(RouteCollectorProxy $hospitalPatient) {
                $hospitalPatient->get('/register', [HospitalController::class, 'registerPatientView']);
                $hospitalPatient->post('/register', [HospitalController::class, 'registerPatient']);
                $hospitalPatient->get('/view', [HospitalController::class, 'patientView']);
                $hospitalPatient->get('/archive/{patient}', [HospitalController::class, 'approvedToArchivePatient']);
                $hospitalPatient->get('/reject/{patient}', [HospitalController::class, 'rejectPatient']);
                $hospitalPatient->get('/accept/{patient}', [HospitalController::class, 'approvePatient']);
                $hospitalPatient->get('/load', [HospitalController::class, 'hospitalPatientLoad']);
                $hospitalPatient->get('/pending', [HospitalController::class, 'patientPendingView']);
                $hospitalPatient->get('/accepted/archive', [HospitalController::class, 'patientAcceptedArchiveView']);
                $hospitalPatient->get('/declined/archive', [HospitalController::class, 'patientDeclinedArchiveView']);
                $hospitalPatient->get('/recover/accepted/{patient}', [HospitalController::class, 'recoverAcceptedPatient']);
                $hospitalPatient->delete('/accepted/archive/{patient}', [HospitalController::class, 'deleteAcceptedPatient']);
                $hospitalPatient->get('/accepted/archive/load', [HospitalController::class, 'patientAcceptedArchiveLoad']);
                $hospitalPatient->get('/declined/archive/load', [HospitalController::class, 'patienDeclinedArchiveLoad']);
                $hospitalPatient->get('/pending/load', [HospitalController::class, 'patientPendingLoad']);
           });

            $hospital->group('/admissionform', function(RouteCollectorProxy $admission) {
                $admission->post('', [AdmissionFormController::class, 'submitForm']);
                $admission->get('/{admissionForm}/edit', [AdmissionFormController::class, 'getAdmission']);
                $admission->post('/{admissionForm}', [AdmissionFormController::class, 'update']);
                $admission->delete('/{admissionForm}', [AdmissionFormController::class, 'delete']);
                $admission->get('/show/{patient}', [AdmissionFormController::class, 'getAdmissionPatient']);
                $admission->get('/referred/show/{patient}', [AdmissionFormController::class, 'getAdmissionReferred']);
                $admission->get('/view', [AdmissionFormController::class, 'admissionPatient']);
                $admission->get('/request/view', [AdmissionFormController::class, 'admissionRequestView']);
                $admission->get('/{admissionForm}/request/{requestAdmission}', [AdmissionFormController::class, 'admisionRequestCompleted']);
                $admission->get('/request/load', [AdmissionFormController::class, 'admissionRequestLoad']);
                $admission->get('/pdf/{admissionForm}', [AdmissionFormController::class, 'admissionPdf']);
                $admission->get('/load', [AdmissionFormController::class, 'showAdmissionLoad']);
                });

            $hospital->group('/checkupform', function(RouteCollectorProxy $checkup) {
                $checkup->post('', [CheckupFormController::class, 'submitForm']);
                $checkup->get('/show/{patient}', [CheckupFormController::class, 'getCheckupPatient']);
                $checkup->post('/{prenatalCheckup}', [CheckupFormController::class, 'update']);
                $checkup->delete('/{prenatalCheckup}', [CheckupFormController::class, 'delete']);
                $checkup->get('/{prenatalCheckup}/edit', [CheckupFormController::class, 'getCheckup']);
                $checkup->get('/referred/show/{patient}', [CheckupFormController::class, 'getCheckupReferred']);
                $checkup->get('/load', [CheckupFormController::class, 'showCheckupLoad']);
                $checkup->get('/pdf/{prenatalCheckup}', [CheckupFormController::class, 'checkupPdf']);
                $checkup->get('/request/view', [CheckupFormController::class, 'checkupRequestView']);
                $checkup->get('/request/load', [CheckupFormController::class, 'checkupRequestLoad']);
                $checkup->get('/{prenatalCheckup}/request/{requestCheckup}', [CheckupFormController::class, 'checkupRequestCompleted']);
                });

            $hospital->group('/medicalform', function(RouteCollectorProxy $medical) {
                $medical->post('', [MedicalCertificateController::class, 'submitForm']);
                $medical->post('/{medicalCertificate}', [MedicalCertificateController::class, 'update']);
                $medical->delete('/{medicalCertificate}', [MedicalCertificateController::class, 'delete']);
                $medical->get('/show/{patient}', [MedicalCertificateController::class, 'getMedicalPatient']);
                $medical->get('/{medicalCertificate}/edit', [MedicalCertificateController::class, 'getMedical']);
                $medical->get('/referred/show/{patient}', [MedicalCertificateController::class, 'getMedicalReferred']);
                $medical->get('/load', [MedicalCertificateController::class, 'showMedicalLoad']);
                $medical->get('/pdf/{medicalCertificate}', [MedicalCertificateController::class, 'medicalPdf']);
                $medical->get('/request/view', [MedicalCertificateController::class, 'medicalRequestView']);
                $medical->get('/{medicalCertificate}/request/{requestMedical}', [MedicalCertificateController::class, 'medicalRequestCompleted']);
                $medical->get('/request/load', [MedicalCertificateController::class, 'medicalRequestLoad']);
                });

            $hospital->group('/refer', function(RouteCollectorProxy $refer) {
                $refer->post('',[ ReferPatientController::class, 'submitForm']);
                $refer->get('/show',[ ReferPatientController::class, 'getReferralRequest']);
                $refer->get('/data/show',[ ReferPatientController::class, 'referralHospital']);
                $refer->get('/accepted/show',[ ReferPatientController::class, 'getReferredRequest']);
                $refer->delete('/reject/{referral}',[ ReferPatientController::class, 'rejectReferral']);
                $refer->get('/accept/{referral}',[ ReferPatientController::class, 'acceptReferral']);
                $refer->get('/{referral}/referral',[ ReferPatientController::class, 'referralPdf']);
                $refer->get('/load',[ ReferPatientController::class, 'pendingLoad']);
                $refer->get('/data/load',[ ReferPatientController::class, 'hospitalReferralLoad']);
                $refer->get('/accepted/load',[ ReferPatientController::class, 'acceptedLoad']);


                });
        })->add(HospitalMiddleware::class);

        //Hospital Pending
        $group->group('/hospital/pending', function (RouteCollectorProxy $hospital) {
            $hospital->get('',[ HospitalPendingController::class, 'index']);
            $hospital->get('/logout', [AuthHospitalController::class, 'logout']);
        })->add(PendingHospitalMiddleware::class);

        //Hospital Archive
        $group->group('/hospital/archive', function (RouteCollectorProxy $hospital) {
            $hospital->get('',[ HospitalArchiveController::class, 'index']);
            $hospital->get('/logout', [AuthHospitalController::class, 'logout']);
        })->add(PendingHospitalMiddleware::class);

        //Doctor Pending
        $group->group('/doctor/pending', function (RouteCollectorProxy $doctor) {
            $doctor->get('',[ DoctorPendingController::class, 'index']);
            $doctor->get('/logout', [AuthDoctorController::class, 'logout']);
        })->add(PendingDoctorMiddleware::class);

        //Doctor Archive
        $group->group('/doctor/archive', function (RouteCollectorProxy $doctor) {
            $doctor->get('',[ DoctorArchiveController::class, 'index']);
            $doctor->get('/logout', [AuthDoctorController::class, 'logout']);
        })->add(PendingDoctorMiddleware::class);

        //Patient Pending
        $group->group('/patient/pending', function (RouteCollectorProxy $patient) {
            $patient->get('',[ PatientPendingController::class, 'index']);
            $patient->get('/logout', [AuthPatientController::class, 'logout']);
        })->add(PendingPatientMiddleware::class);

        //Patient Archive
        $group->group('/patient/archive', function (RouteCollectorProxy $patient) {
            $patient->get('',[ PatientArchiveController::class, 'index']);
            $patient->get('/logout', [AuthPatientController::class, 'logout']);
        })->add(PendingPatientMiddleware::class);

        // DOCTOR ROUTE
        $group->group('/doctor', function (RouteCollectorProxy $doctor) {
           $doctor->get('/profile', [DoctorController::class, 'profile']);
           $doctor->post('/changepassword', [DoctorController::class, 'changePassword']);
           $doctor->get('/changepassword', [DoctorController::class, 'changePasswordView']);
           $doctor->get('', [DoctorController::class, 'index']);
           $doctor->get('/logout', [AuthDoctorController::class, 'logout']);
            
           $doctor->group('/admissionform', function(RouteCollectorProxy $admissioForm) {
                $admissioForm->get('', [DoctorController::class, 'admissionForm']);
                $admissioForm->get('/load', [DoctorController::class, 'admissionLoad']);
                
           });

           $doctor->group('/checkupform', function(RouteCollectorProxy $checkupForm) {
            $checkupForm->get('', [DoctorController::class, 'prescription']);
                $checkupForm->get('/pending', [DoctorController::class, 'pendingPrescription']);
                $checkupForm->post('/{prenatalCheckup}/prescription', [DoctorController::class, 'storePrescription']);
                $checkupForm->get('/pending/load', [DoctorController::class, 'pendingPrescriptionLoad']);
                $checkupForm->get('/load', [DoctorController::class, 'prescriptionLoad']);
           });

        })->add(DoctorMiddleware::class);

        //PATIENT ROUTE
        $group->group('/patient', function (RouteCollectorProxy $patient) {
           $patient->get('/profile', [PatientController::class, 'profile']);
           $patient->post('/changepassword', [PatientController::class, 'changePassword']);
           $patient->get('/changepassword', [PatientController::class, 'changePasswordView']);
           $patient->get('', [PatientController::class, 'index']); 
           $patient->get('/logout', [AuthPatientController::class, 'logout']);

           $patient->group('/admissionform', function (RouteCollectorProxy $admissionForm) {
                $admissionForm->get('', [PatientController::class, 'admissionForm']);
                $admissionForm->get('/request', [PatientController::class, 'admissionRequestView']);
                $admissionForm->get('/{admissionForm}/referral', [PatientController::class, 'getReferral']);
                $admissionForm->get('/load', [PatientController::class, 'admissionLoad']);
                $admissionForm->get('/{admissionForm}/pdf', [AdmissionFormController::class, 'patientadmissionPdf']);
                $admissionForm->get('/request/load', [PatientController::class, 'AdmissionRequestLoad']);
                $admissionForm->get('/{admissionForm}/requested', [PatientController::class, 'requestAdmission']);
            
           });
            $patient->group('/checkupform', function (RouteCollectorProxy $checkupForm) {
                $checkupForm->get('', [PatientController::class, 'checkupForm']);
                $checkupForm->get('/request', [PatientController::class, 'checkupRequestView']);
                $checkupForm->get('/load', [PatientController::class, 'checkupformLoad']);
                $checkupForm->get('/request/load', [PatientController::class, 'checkupRequestLoad']);
                $checkupForm->get('/{prenatalCheckup}/referral', [PatientController::class, 'getCheckupReference']);
                $checkupForm->get('/{prenatalCheckup}/pdf', [CheckupFormController::class, 'patientCheckupPdf']);
                $checkupForm->get('/prescription/{prenatalCheckup}', [PatientController::class, 'getPrescriptionImage']);
                $checkupForm->get('/{prenatalCheckup}/requested', [PatientController::class, 'requestCheckup']);
            });
            $patient->group('/medicalform', function (RouteCollectorProxy $medicalForm) {
                $medicalForm->get('', [PatientController::class, 'medicalForm']);
                $medicalForm->get('/request', [PatientController::class, 'medicalRequestView']);
                $medicalForm->get('/load', [PatientController::class, 'medicalformLoad']);
                $medicalForm->get('/request/load', [PatientController::class, 'medicalRequestLoad']);
                $medicalForm->get('/{medicalCertificate}/requested', [PatientController::class, 'requestMedical']);
                $medicalForm->get('/{medicalCertificate}/pdf', [MedicalCertificateController::class, 'patientMedicalPdf']);
                $medicalForm->get('/{medicalCertificate}/referral', [PatientController::class, 'getMedicalReference']);
            });


        })->add(PatientMiddleware::class);


        //Hospital Route
         })->add(AuthMiddleware::class);


    
        

    //Auth Route
    $app->group('', function (RouteCollectorProxy $group) {
        
        $group->get('/', [AuthPatientController::class, 'loginView']);

        $group->group('/admin', function (RouteCollectorProxy $adminauth) {
            $adminauth->get('/login', [AuthAdminController::class, 'loginView']);
            $adminauth->get('/register', [AuthAdminController::class, 'registerView']);
            $adminauth->post('/register', [AuthAdminController::class, 'register']);
            $adminauth->post('/login', [AuthAdminController::class, 'logIn']);
            $adminauth->post('/login/two-factor', [AuthAdminController::class, 'twoFactorLogin']);
        });

        $group->group('/doctor', function (RouteCollectorProxy $doctor) {
            $doctor->get('/login', [AuthDoctorController::class, 'loginView']);
            $doctor->get('/register', [AuthDoctorController::class, 'registerView']);
            $doctor->post('/register', [AuthDoctorController::class, 'register']);
            $doctor->post('/login', [AuthDoctorController::class, 'logIn']);
            $doctor->post('/login/two-factor', [AuthDoctorController::class, 'twoFactorLogin']);
        });

        $group->group('/hospital', function (RouteCollectorProxy $hospital) {
            $hospital->get('/login', [AuthHospitalController::class, 'loginView']);
            $hospital->post('/login', [AuthHospitalController::class, 'logIn']);
            $hospital->get('/register', [AuthHospitalController::class, 'registerView']);
            $hospital->post('/register', [AuthHospitalController::class, 'register']);
            $hospital->post('/login/two-factor', [AuthHospitalController::class, 'twoFactorLogin']);
        });

        $group->group('/patient', function (RouteCollectorProxy $patient) {
        $patient->post('/login', [AuthPatientController::class, 'logIn']);
        $patient->get('/register', [AuthPatientController::class, 'registerView']);
        $patient->post('/register', [AuthPatientController::class, 'register']);
        $patient->post('/login/two-factor', [AuthPatientController::class, 'twoFactorLogin']);
        });
    })->add(GuestMiddleware::class);

};