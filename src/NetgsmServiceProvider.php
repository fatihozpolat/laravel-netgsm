<?php

namespace Fatihozpolat\Netgsm;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Fatihozpolat\Netgsm\Commands\NetgsmCommand;

class NetgsmServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-netgsm')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-netgsm_table')
            ->hasCommand(NetgsmCommand::class);
    }
}
