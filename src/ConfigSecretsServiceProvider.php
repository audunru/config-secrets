<?php

namespace audunru\ConfigSecrets;

use audunru\ConfigSecrets\Contracts\SecretGateway;
use audunru\ConfigSecrets\Helpers\ConfigurationHelper;
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

        $gateway = ConfigurationHelper::getDefaultGateway();
        $app->singleton($gateway);
        $app->singleton(UpdateConfiguration::class);
        $app->when(UpdateConfiguration::class)
            ->needs(SecretGateway::class)
            ->give($gateway);
    }

    /**
     * Update configuration.
     */
    public static function updateConfiguration(Application $app): void
    {
        if (! $app->configurationIsCached() && ConfigurationHelper::isEnabled() && ! $app->resolved(UpdateConfiguration::class)) {
            ($app->make(UpdateConfiguration::class))();
        }
    }
}
