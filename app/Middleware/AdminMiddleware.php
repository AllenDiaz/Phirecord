<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\AdminProviderServiceInterface;

class AdminMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
        private readonly AdminProviderServiceInterface $adminProvider
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->session->get('hospital')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/hospital');
        }
        if ($this->session->get('doctor')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/doctor');
        }
        if ($this->session->get('patient')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/patient');
        }
        if ($adminId = $this->session->get('admin')) {
            $admin = $this->adminProvider->getById($adminId);

            $isHeadAdmin = $admin->getIsHeadAdmin();
            if($isHeadAdmin){
                return $this->responseFactory->createResponse(302)->withHeader('Location', '/admin/head');
            }
        }


        return $handler->handle($request);
    }
}
