<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->session->get('admin')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/admin');
        }
        if ($this->session->get('hospital')) {
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/hospital');
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
