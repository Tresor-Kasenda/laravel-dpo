<?php

/*---------------------------------------------------------
 * You can place your custom package configuration in here.
 * This file will be copied to config/laravel_dpo.php when you run
 * php artisan vendor:publish --tag=laravel_dpo-config
 * --------------------------------------------------------*/

return [
    'company' => [
        'token' => env('COMPANY_TOKEN', ''),
        'type' => env('COMPANY_TYPE', '3854'),
    ],
    'test_mode' => env('DPO_TEST_MODE', true),
    'back_url' => env('DPO_BACK_URL'),
    'redirect_url' => env('DPO_REDIRECT_URL'),
];