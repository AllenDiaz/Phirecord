<?php

declare(strict_types = 1);

namespace App\Mail;

use App\Config;
use App\Entity\AdminLoginCode;
use App\Entity\DoctorLoginCode;
use App\Entity\PatientLoginCode;
use App\Entity\HospitalLoginCode;
use PHPMailer\PHPMailer\PHPMailer;

class TwoFactorAuthEmail
{
    public function __construct(private readonly Config $config) {

    }

    public function send(AdminLoginCode $adminLoginCode): void
    {
    // Set sender
        $mailer = new PHPMailer(true);
        // Enable verbose debugging for development (optional)
        // $this->mailer->SMTPDebug = 2;
        // Set mailer to use SMTP
        $mailer->isSMTP();
        // SMTP configuration
        $mailer->Host = 'smtp.gmail.com'; // Your SMTP host
        $mailer->SMTPAuth = true;
        $mailer->Username = 'phirecord.pangasinan@gmail.com'; // Your SMTP username
        $mailer->Password = 'dvndzxgzkqrkbicz'; // Your SMTP password
        $mailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mailer->Port = 587; // TCP port to connect to
            $mailer->setFrom('phirecord.pangasinan@gmail.com', 'PhiRecord Support');
            // Add recipient
            $mailer->addAddress($adminLoginCode->getAdmin()->getEmail());
            // Email subject
            $mailer->isHTML(true);
            $mailer->Subject = 'PhiRecord OTP Verification';
            // Email body
            $mailer->Body = '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">PhiRecord</a>
    </div>
    <p style="font-size:1.1em">Hi,</p>
    <p>Good day '. $adminLoginCode->getAdmin()->getName() .'. Use the following OTP to complete your Log in procedures. OTP is valid for 5 minutes</p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'. $adminLoginCode->getCode() .'</h2>
    <p style="font-size:0.9em;">Regards,<br />PhiRecord</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <p>PhiRecord</p>
      <p>Umingan Pangasinan Nueva Ecija</p>
      <p>Philippines</p>
    </div>
  </div>
</div>';

            // Send email
    $mailer->send();
            // return true;  // Email sent successfully
    }

    public function sendHospital(HospitalLoginCode $hospitalLoginCode): void
    {
    // Set sender
        $mailer = new PHPMailer(true);
        // Enable verbose debugging for development (optional)
        // $this->mailer->SMTPDebug = 2;
        // Set mailer to use SMTP
        $mailer->isSMTP();
        // SMTP configuration
        $mailer->Host = 'smtp.gmail.com'; // Your SMTP host
        $mailer->SMTPAuth = true;
        $mailer->Username = 'phirecord.pangasinan@gmail.com'; // Your SMTP username
        $mailer->Password = 'dvndzxgzkqrkbicz'; // Your SMTP password
        $mailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mailer->Port = 587; // TCP port to connect to
            $mailer->setFrom('phirecord.pangasinan@gmail.com', 'PhiRecord Support');
            // Add recipient
            $mailer->addAddress($hospitalLoginCode->getHospital()->getEmail());
            // Email subject
            $mailer->isHTML(true);
            $mailer->Subject = 'PhiRecord OTP Verification';
            // Email body
            $mailer->Body = '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">PhiRecord</a>
    </div>
    <p style="font-size:1.1em">Hi,</p>
    <p>Good day '. $hospitalLoginCode->getHospital()->getName() .'. Use the following OTP to complete your Log in procedures. OTP is valid for 5 minutes</p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'. $hospitalLoginCode->getCode() .'</h2>
    <p style="font-size:0.9em;">Regards,<br />PhiRecord</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <p>PhiRecord</p>
      <p>Umingan Pangasinan Nueva Ecija Philippines</p>
      <p>2024</p>
    </div>
  </div>
</div>';

            // Send email
    $mailer->send();
            // return true;  // Email sent successfully
    }

    public function sendDoctor(DoctorLoginCode $doctorLoginCode): void
    {
    // Set sender
        $mailer = new PHPMailer(true);
        // Enable verbose debugging for development (optional)
        // $this->mailer->SMTPDebug = 2;
        // Set mailer to use SMTP
        $mailer->isSMTP();
        // SMTP configuration
        $mailer->Host = 'smtp.gmail.com'; // Your SMTP host
        $mailer->SMTPAuth = true;
        $mailer->Username = 'phirecord.pangasinan@gmail.com'; // Your SMTP username
        $mailer->Password = 'dvndzxgzkqrkbicz'; // Your SMTP password
        $mailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mailer->Port = 587; // TCP port to connect to
            $mailer->setFrom('phirecord.pangasinan@gmail.com', 'PhiRecord Support');
            // Add recipient
            $mailer->addAddress($doctorLoginCode->getDoctor()->getEmail());
            // Email subject
            $mailer->isHTML(true);
            $mailer->Subject = 'PhiRecord OTP Verification';
            // Email body
            $mailer->Body = '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">PhiRecord</a>
    </div>
    <p style="font-size:1.1em">Hi,</p>
    <p>Good day '. $doctorLoginCode->getDoctor()->getName() .'. Use the following OTP to complete your Log in procedures. OTP is valid for 5 minutes</p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'. $doctorLoginCode->getCode() .'</h2>
    <p style="font-size:0.9em;">Regards,<br />PhiRecord</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <p>PhiRecord</p>
      <p>Umingan Pangasinan Nueva Ecija Philippines</p>
      <p>2024</p>
    </div>
  </div>
</div>';

            // Send email
    $mailer->send();
            // return true;  // Email sent successfully
    }

    public function sendPatient(PatientLoginCode $patientLoginCode): void
    {
    // Set sender
        $mailer = new PHPMailer(true);
        // Enable verbose debugging for development (optional)
        // $this->mailer->SMTPDebug = 2;
        // Set mailer to use SMTP
        $mailer->isSMTP();
        // SMTP configuration
        $mailer->Host = 'smtp.gmail.com'; // Your SMTP host
        $mailer->SMTPAuth = true;
        $mailer->Username = 'phirecord.pangasinan@gmail.com'; // Your SMTP username
        $mailer->Password = 'dvndzxgzkqrkbicz'; // Your SMTP password
        $mailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mailer->Port = 587; // TCP port to connect to
            $mailer->setFrom('phirecord.pangasinan@gmail.com', 'PhiRecord Support');
            // Add recipient
            $mailer->addAddress($patientLoginCode->getPatient()->getEmail());
            // Email subject
            $mailer->isHTML(true);
            $mailer->Subject = 'PhiRecord OTP Verification';
            // Email body
            $mailer->Body = '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">PhiRecord</a>
    </div>
    <p style="font-size:1.1em">Hi,</p>
    <p>Good day '. $patientLoginCode->getPatient()->getName() .'. Use the following OTP to complete your Log in procedures. OTP is valid for 5 minutes</p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'. $patientLoginCode->getCode() .'</h2>
    <p style="font-size:0.9em;">Regards,<br />PhiRecord</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <p>PhiRecord</p>
      <p>Umingan Pangasinan Nueva Ecija Philippines</p>
      <p>2024</p>
    </div>
  </div>
</div>';

            // Send email
    $mailer->send();
            // return true;  // Email sent successfully
    }
}
