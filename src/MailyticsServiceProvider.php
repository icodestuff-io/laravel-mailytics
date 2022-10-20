<?php

namespace Icodestuff\Mailytics;

use Icodestuff\Mailytics\Commands\MailyticsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailyticsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-mailytics')
            ->hasConfigFile('mailytics')
            ->hasViews('mailytics')
            ->hasMigration('create_mailytics_table')
            ->hasRoute('web')
            ->hasCommand(MailyticsCommand::class);
    }
}
