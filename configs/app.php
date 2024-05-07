<?php

declare(strict_types = 1);

use App\Enum\StorageDriver;
use App\Enum\AppEnvironment;

$appEnv = $_ENV['APP_ENV'] ?? AppEnvironment::Production->value;

$appSnakeName = strtolower(str_replace(' ', '_', $_ENV['APP_NAME']));

return [
    'app_name'              => $_ENV['APP_NAME'],
    'app_version'           => $_ENV['APP_VERSION'] ?? '1.0',
    'app_environment'       => $appEnv,
    'display_error_details' => (bool) ($_ENV['APP_DEBUG'] ?? 0),
    'log_errors'            => true,
    'log_error_details'     => true,
    'doctrine'              => [
        'dev_mode'   => AppEnvironment::isDevelopment($appEnv),
        'cache_dir'  => STORAGE_PATH . '/cache/doctrine',
        'entity_dir' => [APP_PATH . '/Entity'],
        'connection' => [
            'driver'   => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
            'host'     => $_ENV['DB_HOST'] ?? 'localhost',
            'port'     => $_ENV['DB_PORT'] ?? 3306,
            'dbname'   => $_ENV['DB_NAME'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
        ],
    ],
    'session'               => [
        'name'  => $appSnakeName . '_session',
        'flash_name' => $appSnakeName . '_flash',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'lax',
],
    'storage'               => [
        'driver' => StorageDriver::Local,
    ],
    'twillio' => [
        'account_sid' => 'ACe365913962c82b8060bc3b43a8e7153b',
        'account_token' => 'a6d3228c4cf3d9a0f8b3fe7de2f48b55',
        'account_number' => '+1 656 220 5700',
    ],
    'smtp' => [
        'email' => 'phirecord.pangasinan@gmail.com',
        'password' => 'dvndzxgzkqrkbicz',
    ],
];
