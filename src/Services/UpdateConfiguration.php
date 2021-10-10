<?php

namespace audunru\ConfigSecrets\Services;

use audunru\ConfigSecrets\Contracts\SecretGateway;
use audunru\ConfigSecrets\Helpers\ConfigurationHelper;

class UpdateConfiguration
{
    /**
     * @SuppressWarnings("unused")
     */
    public function __construct(private SecretGateway $gateway)
    {
    }

    /**
     * Load secrets and update configuration.
     */
    public function updateConfiguration(): void
    {
        logger()->info(sprintf('Retrieving secrets using %s', get_class($this->gateway)));
        $secrets = $this->gateway->getSecrets();
        logger()->info(sprintf('Retrieved %u secrets', $secrets->count()));

        ConfigurationHelper::updateConfiguration($secrets);
    }
}
