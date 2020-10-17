<?php

return [
    /*
     * Add all Slick API's you like to connect with here
     */
    'apis' => [
        'productapi-local' => [
            'url' => 'https://productapi2.alfana.localhost',
            'name' => 'Alfana ProductAPI',
            'token' => '51dbc56e4dc8893e649f70021268f30cb1ad244a',
            'version' => null,
            'verify' => false,
            'cache_key' => 'productapi_local',
            'query' => [
                'shop_id' => env('SHOP_ID', 1)
            ]
        ],
        'productapi-live' => [
            'url' => 'https://productapi2.alfana.nl',
            'name' => 'Alfana ProductAPI',
            'token' => 'cc4e511c077e94934ae07483559e1724476d9a4c',
            'version' => null,
            'verify' => true,
            'cache_key' => 'productapi_live',
            'query' => [
                'shop_id' => env('SHOP_ID', 1)
            ]
        ],
        'customerapi-live' => [
            'url' => 'https://customer.alfana.nl',
            'name' => 'Alfana CustomerAPI',
            'token' => '94a59e9b836f1a9e297fb52a760a1b97d51b8d3a',
            'version' => null,
            'verify' => true,
            'cache_key' => 'customerapi_live'
        ],
        'customerapi-local' => [
            'url' => 'https://customer.alfana.localhost',
            'name' => 'Alfana CustomerAPI',
            'token' => '94a59e9b836f1a9e297fb52a760a1b97d51b8d3a',
            'version' => null,
            'verify' => true,
            'cache_key' => 'customerapi_live'
        ],
        'shopsapi-live' => [
            'url' => 'https://shops.alfana.nl/api/index.php',
            'name' => 'Alfana Shops API',
            'token' => '543345535',
            'version' => null,
            'verify' => true,
            'cache_key' => 'shopsapi_live',
            'query' => [
                'shop' => env('SHOP_ID', 1)
            ]
        ],
        'shopsapi-local' => [
            'url' => 'http://localhost/shops/api/index.php',
            'name' => 'Alfana Shops API',
            'token' => '543345535',
            'version' => null,
            'verify' => true,
            'cache_key' => 'shopsapi_local',
            'query' => [
                'shop' => env('SHOP_ID', 1)
            ]
        ],
        'dashboard-local' => [
            'url' => 'http://localhost/dashboard/api',
            'name' => 'Dashboard API',
            'token' => 'a1e3d8d728183716ebaa3fd3910d9403e988a8ba',
            'version' => null,
            'verify' => true,
            'cache_key' => 'dashboard_local',
            'query' => [
                'shop' => env('SHOP_ID', 1)
            ]
        ],

    ],
    'shop_id' => env('SHOP_ID'),
    'product_api_env' => env('PRODUCT_API_ENV'),
    'shops_api_env' => env('SHOPS_API_ENV'),
    'dashboard_api_env' => env('DASHBOARD_API_ENV'),
    'customer_api_env' => env('CUSTOMER_API_ENV')
];
