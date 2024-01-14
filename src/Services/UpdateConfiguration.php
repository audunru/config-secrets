<?php

namespace audunru\ConfigSecrets\Services;

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigProviders\AwsConfigProvider;
use audunru\ConfigSecrets\Contracts\ConfigProvider;
use Exception;

class UpdateConfiguration
{
    /**
     * Load secrets and update configuration.
     */
    public function __invoke(): void
    {
        // logger()->info(sprintf('Retrieving secrets using %s', get_class($this->gateway)));
        // $secrets = $this->gateway->getSecrets();
        // logger()->info(sprintf('Retrieved %u secrets', $secrets->count()));

        // ConfigurationHelper::updateEnvironmentConfiguration();
        // ConfigurationHelper::updateConfiguration($secrets);

        $environmentConfig = $this->getEnvironmentConfig();

        foreach ($environmentConfig as $providerName => $options) {
            if (is_int($providerName)) {
                $providerName = $options;
                $options = [];
            }

            $providerOptions = $this->getProviderOptions($providerName);
            $provider = $this->getProvider($providerName);
            $overrides = $provider->getOverrides(array_merge_recursive($providerOptions, $options));

            $this->updateConfiguration($overrides);
        }
    }

    private function getEnvironmentConfig(): array
    {
        return config('config-secrets.environments.'.config('app.env'), []);
    }

    private function getProviderOptions(string $providerName): array
    {
        return config('config-secrets.providers.'.$providerName, []);
    }

    private function getProvider(string $providerName): ConfigProvider
    {
        if ('array' === $providerName) {
            return app()->make(ArrayConfigProvider::class); // TODO: Hent fra config
        }

        if ('aws' === $providerName) {
            return app()->make(AwsConfigProvider::class);
        }

        throw new Exception('No provider named '.$providerName.' exists');
    }

    private function updateConfiguration(array $overrides): void
    {
        config($overrides);
    }
}
