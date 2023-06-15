<?php

namespace Scott\LaravelDpo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDPOServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dpo')
            ->hasConfigFile()
            ->hasMigration('create_laravel_dpo_table');
    }
}
