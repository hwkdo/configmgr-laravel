<?php

namespace Hwkdo\ConfigmgrLaravel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Hwkdo\ConfigmgrLaravel\Commands\ConfigmgrLaravelCommand;

class ConfigmgrLaravelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('configmgr-laravel')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_configmgr_laravel_table')
            ->hasCommand(ConfigmgrLaravelCommand::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->app['config']->set('database.connections.sccm', config('configmgr-laravel.connection'));
    }
}
