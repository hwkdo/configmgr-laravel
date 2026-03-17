<?php

// config for Hwkdo/ConfigmgrLaravel
return [
    'connection' => [
        'driver' => 'sqlsrv',
        'url' => env('SCCM_DB_URL'),
        'host' => env('SCCM_DB_HOST', '192.168.120.64'),
        'port' => env('SCCM_DB_PORT', '1433'),
        'database' => env('SCCM_DB_DATABASE', 'CM_HWK'),
        'username' => env('SCCM_DB_USERNAME', 'svc_sccm_sql'),
        'password' => env('SCCM_DB_PASSWORD', ''),
        'charset' => env('SCCM_DB_CHARSET', 'SQL_Latin1_CP1_CI_AS'),
        'prefix' => '',
        'prefix_indexes' => true,
        'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'true'),
        'login_timeout' => (int) env('SCCM_DB_LOGIN_TIMEOUT', 10),
    ],
];
