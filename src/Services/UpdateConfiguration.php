<?php

namespace audunru\ConfigSecrets\Services;

use audunru\ConfigSecrets\Contracts\SecretGateway;
use audunru\ConfigSecrets\Helpers\SecretsHelper;

class UpdateConfiguration
{
    private int $updates = 0;

    /**
     * Load secrets and update configuration.
     */
    public function updateConfiguration(): void
    {
        if (! $this->isEnabled() || $this->hasUpdated()) {
            return;
        }

        ++$this->updates;

        $service = $this->getSecretService();

        logger()->info(sprintf('Retrieving secrets using %s', get_class($service)));
        $secrets = $service->getSecrets();
        logger()->info(sprintf('Retrieved %u secrets', $secrets->count()));

        SecretsHelper::updateConfiguration($secrets);
    }

    /**
     * Check if secrets service is enabled.
     */
    protected function isEnabled(): bool
    {
        return in_array(config('app.env'), config('config-secrets.enabled-environments', []));
    }

    /**
     * Check if configuration has been updated.
     */
    protected function hasUpdated(): bool
    {
        return $this->updates > 0;
    }

    /**
     * Get a new instance of 3rd party service that holds secrets.
     */
    protected function getSecretService(): SecretGateway // TODO: when UpdateConfiguration::class needs SecretGateway, give....https://laravel.com/docs/8.x/container#the-make-method
    {
        $serviceName = config('config-secrets.default');
        $serviceClass = config(sprintf('config-secrets.%s.service', $serviceName));
        app()->singletonIf($serviceClass);

        return app()->make($serviceClass);
    }
}
