<?php

declare(strict_types=1);

return [
    'enabled' => env('BESTCHANGE_PARSER_ENABLED', false),
    'proxy' => [
        'enabled' => env('BESTCHANGE_PARSER_PROXY_ENABLED', true),
        // https://brightdata.com/
        'ip_list' => [
            '185.251.249.156',
            '141.193.98.80',
            '31.204.55.168',
            '91.232.57.141',
            '154.7.229.70',
            '154.17.130.228',
            '154.7.229.109',
            '154.7.229.8',
            '103.14.104.69',
            '31.204.55.226',
        ],
        'requests_per_ip' => 30,
        'host' => 'brd.superproxy.io:22225',
        'username' => 'brd-customer-hl_86a377c7',
        'password' => 'rd9ruirsf8x7',
    ]
];
