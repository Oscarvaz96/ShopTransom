<?php
return [
    'backend' => [
        'frontName' => 'admin_1geu31'
    ],
    'crypt' => [
        'key' => '4cd8e17e48e12af3e987d6ade92e4a1f'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'ShopTransom4',
                'username' => 'magento',
                'password' => 'M4gent0Us3r%6',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => '8b5_'
            ],
            'page_cache' => [
                'id_prefix' => '8b5_'
            ]
        ]
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => NULL
        ]
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1,
        'compiled_config' => 1
    ],
    'install' => [
        'date' => 'Wed, 29 Jan 2020 17:12:00 +0000'
    ]
];
