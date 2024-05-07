<?php

declare(strict_types = 1);

use App\Auth;
use App\Csrf;
use Slim\App;
use App\Config;
use App\Session;
use App\AuthAdmin;
use App\AuthDoctor;
use App\AuthPatient;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use App\AuthHospital;
use App\Enum\SameSite;
use function DI\create;
use Doctrine\ORM\ORMSetup;
use App\Enum\StorageDriver;
use App\Enum\AppEnvironment;
use Slim\Factory\AppFactory;
use Doctrine\ORM\EntityManager;
use App\Contracts\AuthInterface;
use Doctrine\DBAL\DriverManager;
use League\Flysystem\Filesystem;
use App\DataObjects\SessionConfig;
use Twig\Extra\Intl\IntlExtension;
use App\Contracts\SessionInterface;
use App\RouteEntityBindingStrategy;
use Symfony\Component\Asset\Package;
use App\Contracts\AuthAdminInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Asset\Packages;
use App\Contracts\AuthDoctorInterface;
use App\Services\AdminProviderService;
use App\Services\EntityManagerService;
use App\Contracts\AuthPatientInterface;
use App\Services\DoctorProviderService;
use App\Contracts\AuthHospitalInterface;
use App\Services\PatientProviderService;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\HospitalProviderService;
use Slim\Interfaces\RouteParserInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Contracts\AdminProviderServiceInterface;
use App\Contracts\EntityManagerServiceInterface;
use App\Contracts\DoctorProviderServiceInterface;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use App\Contracts\PatientProviderServiceInterface;
use App\RequestValidators\RequestValidatorFactory;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use App\Contracts\HospitalProviderServiceInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

return [
    App::class                      => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        $addMiddlewares = require CONFIG_PATH . '/middleware.php';
        $router         = require CONFIG_PATH . '/routes/web.php';

        $app = AppFactory::create();
        
        $app->getRouteCollector()->setDefaultInvocationStrategy(
            new RouteEntityBindingStrategy(
                $container->get(EntityManagerServiceInterface::class),
                $app->getResponseFactory()
            )
        );
        $router($app);

        $addMiddlewares($app);


        return $app;
    },
    Config::class                   => create(Config::class)->constructor(require CONFIG_PATH . '/app.php'),
    EntityManagerInterface::class           => function (Config $config) {
        $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
            $config->get('doctrine.entity_dir'),
            $config->get('doctrine.dev_mode')
        );

        return new EntityManager(
            DriverManager::getConnection($config->get('doctrine.connection'), $ormConfig),
            $ormConfig
        );
    },
    Twig::class                     => function (Config $config, ContainerInterface $container) {
        $twig = Twig::create(VIEW_PATH, [
            'cache'       => STORAGE_PATH . '/cache/templates',
        'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
        ]);

        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new EntryFilesTwigExtension($container));
        $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));
        // $twig->addExtension(new AssetExtension('./../../storage'));

        return $twig;
    },
    /**
     * The following two bindings are needed for EntryFilesTwigExtension & AssetExtension to work for Twig
     */
    'webpack_encore.packages'       => fn() => new Packages(
        new Package(new JsonManifestVersionStrategy(BUILD_PATH . '/manifest.json'))
    ),
    'webpack_encore.tag_renderer'   => fn(ContainerInterface $container) => new TagRenderer(
        new EntrypointLookup(BUILD_PATH . '/entrypoints.json'),
        $container->get('webpack_encore.packages')
    ),
    ResponseFactoryInterface::class => fn(App $app) => $app->getResponseFactory(),  
    AuthAdminInterface::class => fn(ContainerInterface $container) => $container->get(
        AuthAdmin::class
    ),
    AuthHospitalInterface::class => fn(ContainerInterface $container) => $container->get(
        AuthHospital::class
    ),
    AuthDoctorInterface::class => fn(ContainerInterface $container) => $container->get(
        AuthDoctor::class
    ),
    AuthPatientInterface::class => fn(ContainerInterface $container) => $container->get(
        AuthPatient::class
    ),
    AdminProviderServiceInterface::class => fn(ContainerInterface $container) => $container->get(
        AdminProviderService::class
    ),
    HospitalProviderServiceInterface::class => fn(ContainerInterface $container) => $container->get(
        HospitalProviderService::class
    ),
    DoctorProviderServiceInterface::class => fn(ContainerInterface $container) => $container->get(
        DoctorProviderService::class
    ),
    PatientProviderServiceInterface::class => fn(ContainerInterface $container) => $container->get(
        PatientProviderService::class
    ),
    RequestValidatorFactoryInterface::class => fn(ContainerInterface $container) => $container->get(
        RequestValidatorFactory::class  
    ),
    EntityManagerServiceInterface::class    => fn(EntityManagerInterface $entityManager) => new EntityManagerService(
        $entityManager
    ),
    'csrf'                                  => fn(ResponseFactoryInterface $responseFactory, Csrf $csrf) 
    => new Guard(
        $responseFactory, failureHandler: $csrf->failureHandler(), persistentTokenMode: true
    ),

    Filesystem::class                       => function (Config $config) {
        $adapter = match ($config->get('storage.driver')) {
            StorageDriver::Local => new LocalFilesystemAdapter(RESOURCES_PATH),
        };

        return new League\Flysystem\Filesystem($adapter);
    },
    
   SessionInterface::class             => fn(Config $config) => new Session(
        new SessionConfig(
            $config->get('session.name', ''),
            $config->get('session.flash_name', 'flash'),
            $config->get('session.secure', true),
            $config->get('session.httponly', true),
            SameSite::from($config->get('session.samesite', 'lax'))
        )),
    
    RouteParserInterface::class             => fn(App $app) => $app->getRouteCollector()->getRouteParser(),
    ];
    
