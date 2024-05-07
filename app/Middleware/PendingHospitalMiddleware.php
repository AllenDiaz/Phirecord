<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\HospitalProviderServiceInterface;

class PendingHospitalMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
        private readonly HospitalProviderServiceInterface $hospitalProviderService
     ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($hospitalId = $this->session->get('hospital')) {
            $hospital = $this->hospitalProviderService->getById($hospitalId );
            $status = $hospital->getStatus();
            $archive = $hospital->getIsArchived();
            if(!$archive && $status === '1') {
                 return $this->responseFactory->createResponse(302)->withHeader('Location', '/hospital');
            }
      
        }

        if ($this->session->get('admin')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/admin');
        }
        if ($this->session->get('doctor')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/doctor');
        }
        if ($this->session->get('patient')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/patient');
        }


        return $handler->handle($request);
    }
}
