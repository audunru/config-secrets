<?php

namespace audunru\ConfigSecrets;

use audunru\ConfigSecrets\Services\UpdateConfiguration;
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
        $this->app->singletonIf(UpdateConfiguration::class);
    }

    public function packageBooted()
    {
        if (! $this->app->configurationIsCached()) {
            $this->app->make(UpdateConfiguration::class)->updateConfiguration();
        }
    }
}
