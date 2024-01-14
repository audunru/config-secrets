<?php

namespace audunru\ConfigSecrets\Services;

use audunru\ConfigSecrets\Contracts\ConfigProvider;
use Exception;

class UpdateConfiguration
{
    /**
     * Load secrets and update configuration.
     */
    public function __invoke(): void
    {
        $environmentConfig = $this->getEnvironmentConfig();

        foreach ($environmentConfig as $providerName => $options) {
            if (is_int($providerName)) {
                $providerName = $options;
                $options = [];
            }

            $providerOptions = $this->getProviderOptions($providerName);
            $provider = $this->getProvider($providerName);
            $overrides = $this->resolveProvider($provider)->getOverrides(array_merge_recursive($providerOptions, $options));

            $this->updateConfiguration($overrides);

            logger()->info(sprintf('ConfigProvider "%s" supplied %u configuration %s', $providerName, count($overrides), count($overrides) > 1 ? 'values' : 'value'));
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

    private function getProvider(string $providerName): string
    {
        $provider = config('config-secrets.providers.'.$providerName.'.provider');

        if (is_null($provider)) {
            throw new Exception('No provider named '.$providerName.' exists'); // @todo test
        }

        return $provider;
    }

    private function resolveProvider(string $provider): ConfigProvider
    {
        return app()->make($provider);
    }

    private function updateConfiguration(array $overrides): void
    {
        config($overrides);
    }
}
