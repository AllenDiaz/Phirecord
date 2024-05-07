<?php

declare(strict_types = 1);

namespace App\Sms;

use App\Config;
use Twilio\Rest\Client;
use App\Entity\UserLoginCode;
use App\Entity\AdminLoginCode;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;

class TwoFactorAuthSms
{
    public function __construct(
        private readonly Config $config,

    ) {
    }

    public function send(AdminLoginCode $adminLoginCode): void
    {
         $toNumber = '+63 956 947 8798';
         $sid = $this->config->get('twillio.account_sid');
         $token = $this->config->get('twillio.account_token');
         $message = 'it is working';
    // Initialize Twilio client
        $twilio = new Client($sid, $token);


        // Send SMS
        $twilio->messages->create(
            $toNumber,
            [
                'from' => $this->config->get('twillio.account_number'),
                'body' => $adminLoginCode->getCode()
            ]
        );


}
}
