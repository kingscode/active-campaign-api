<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    
    'channels' => [
        'activecamaign' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/document-ai.log'),
            'days'   => 7,
        ],
    ],

];
