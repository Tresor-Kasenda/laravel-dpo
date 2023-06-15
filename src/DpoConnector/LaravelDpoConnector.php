<?php

namespace Scott\LaravelDpo\DpoConnector;

class LaravelDpoConnector
{
    public function __construct(
        public string $dpo_url,
        public string $company_token,
        public string $company_type,
        public string $back_url,
        public string $redirect_url,
        public string $gateway_url = '/payv2.php?ID=',
    ) {
    }

    public static function handle(): LaravelDpoConnector
    {
        return new self(
            dpo_url: config('laravel_dpo.test_mode') ? config('laravel_dpo.test_url') : config('laravel_dpo.live_url'),
            company_token: config('laravel_dpo.company.token'),
            company_type: config('laravel_dpo.company.type'),
            back_url: config('laravel_dpo.back_url'),
            redirect_url: config('laravel_dpo.redirect_url'),
        );
    }
}
