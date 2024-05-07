<?php

declare(strict_types = 1);

namespace App\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PatientPendingController
{
    public function __construct(
        private readonly Twig $twig,
    )
{

}
public function index(Response $response): Response
{
    return $this->twig->render($response, 'patient_pending/index.twig',  ['isActive' => ['dashboard' => TRUE],
    ]);
}

    

}
