<?php

namespace audunru\ConfigSecrets;

use audunru\ConfigSecrets\Services\UpdateConfiguration;
use Illuminate\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ConfigSecretsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('config-secrets')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        self::registerDependencies($this->app);
    }

    public function packageBooted()
    {
        self::updateConfiguration($this->app);
    }

    /**
     * Register all dependencies and update configuration with secrets.
     */
    public static function registerAndUpdate(Application $app): void
    {
        self::registerDependencies($app);
        self::updateConfiguration($app);
    }

    /**
     * Register all dependencies.
     */
    public static function registerDependencies(Application $app): void
    {
        if ($app->bound(UpdateConfiguration::class)) {
            return;
        }

        $app->singleton(UpdateConfiguration::class);
    }

    /**
     * Update configuration.
     */
    public static function updateConfiguration(Application $app): void
    {
        if (! $app->configurationIsCached() && ! $app->resolved(UpdateConfiguration::class)) {
            ($app->make(UpdateConfiguration::class))();
        }
    }
}
