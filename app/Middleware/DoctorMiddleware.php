<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\DoctorProviderServiceInterface;

class DoctorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
        private readonly DoctorProviderServiceInterface $doctorProviderService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if ($doctorId = $this->session->get('doctor')) {
            $doctor = $this->doctorProviderService->getById($doctorId );
            $status = $doctor->getStatus();
            $archive = $doctor->getIsArchived();
            if ($archive) {
                return $this->responseFactory->createResponse(302)->withHeader('Location', '/doctor/archive');
            } else if($status === '0') {
                 return $this->responseFactory->createResponse(302)->withHeader('Location', '/doctor/pending');
            } 
        }
        if ($this->session->get('admin')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/admin');
        }
        if ($this->session->get('hospital')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/hospital');
        }
        if ($this->session->get('patient')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/patient');
        }


        return $handler->handle($request);
    }
}
