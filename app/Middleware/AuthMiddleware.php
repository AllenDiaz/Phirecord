<?php

declare(strict_types = 1);

namespace App\Middleware;

use Slim\Views\Twig;
use App\Contracts\AuthAdminInterface;
use App\Contracts\AuthDoctorInterface;
use App\Contracts\AuthPatientInterface;
use Psr\Http\Message\ResponseInterface;
use App\Contracts\AuthHospitalInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\EntityManagerServiceInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly AuthAdminInterface $authAdmin,
        private readonly AuthHospitalInterface $authHospital,
        private readonly AuthDoctorInterface $authDoctor,
        private readonly AuthPatientInterface $authPatient,
        private readonly Twig $twig,
        private readonly EntityManagerServiceInterface $entityManagerService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if ($admin = $this->authAdmin->admin()) {
            $this->twig->getEnvironment()->addGlobal('auth', ['id' => $admin->getId(), 'name' => $admin->getName()]);

            return $handler->handle($request->withAttribute('admin', $admin));
        }

        else if ($hospital = $this->authHospital->hospital()) {
            $this->twig->getEnvironment()->addGlobal('auth', ['id' => $hospital->getId(), 'name' => $hospital->getName()]);

            return $handler->handle($request->withAttribute('hospital', $hospital));
        }

        else if ($doctor = $this->authDoctor->doctor()) {
            $this->twig->getEnvironment()->addGlobal('auth', ['id' => $doctor->getId(), 'name' => $doctor->getName()]);

            return $handler->handle($request->withAttribute('doctor', $doctor));
        }

        else if ($patient = $this->authPatient->patient()) {
            $this->twig->getEnvironment()->addGlobal('auth', ['id' => $patient->getId(), 'name' => $patient->getName()]);

            return $handler->handle($request->withAttribute('patient', $patient));
        }

        return $this->responseFactory->createResponse(302)->withHeader('Location', '/');
    }
}
