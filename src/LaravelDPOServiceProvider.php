<?php

namespace Scott\LaravelDpo;

class LaravelDPOServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-dpo')
            ->hasConfigFile()
            ->hasMigration('create_laravel-dpo_table')
            ->hasCommand(LaravelDPOCommand::class);
    }
}