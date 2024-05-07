<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\PatientProviderServiceInterface;

class PendingPatientMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
        private readonly PatientProviderServiceInterface $patientProviderService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($patientId = $this->session->get('patient')) {
            $patient = $this->patientProviderService->getById($patientId );
            $status = $patient->getStatus();
            $archive = $patient->getIsArchived();
            if(!$archive && $status === '1') {
                 return $this->responseFactory->createResponse(302)->withHeader('Location', '/patient');
            }
        }
        if ($this->session->get('admin')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/admin');
        }
        if ($this->session->get('hospital')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/hospital');
        }
        if ($this->session->get('doctor')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/doctor');
        }


        return $handler->handle($request);
    }
}
