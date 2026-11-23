<?php

namespace audunru\ConfigSecrets\Services;

use audunru\ConfigSecrets\Contracts\ConfigProvider;
use Exception;
use Illuminate\Support\Arr;

class UpdateConfiguration
{
    /**
     * Configuration supplied by each provider in the current PHP process.
     */
    private static array $cache = [];

    /**
     * Clear the configuration kept in memory for the current PHP process.
     */
    public static function flush(): void
    {
        self::$cache = [];
    }

    /**
     * Update configuration.
     */
    public function __invoke(): void
    {
        if (! $this->shouldUpdateConfig()) {
            return;
        }

        $environmentConfig = $this->getEnvironmentConfig();

        foreach ($environmentConfig as $providerName => $options) {
            if (is_int($providerName)) {
                $providerName = $options;
                $options = [];
            }

            $providerOptions = $this->getProviderOptions($providerName);
            $mergedOptions = array_replace_recursive($providerOptions, $options);
            $cacheKey = $providerName.':'.md5(serialize($mergedOptions));

            if (array_key_exists($cacheKey, self::$cache)) {
                $this->updateConfiguration(self::$cache[$cacheKey]);

                continue;
            }

            $provider = $this->getProvider($providerName);
            $configuration = $this->resolveProvider($provider)->getConfiguration($mergedOptions);
            self::$cache[$cacheKey] = $configuration;

            $this->updateConfiguration($configuration);

            if (Arr::get($mergedOptions, 'log', false)) {
                logger()->info(sprintf('ConfigProvider "%s" supplied %u configuration %s', $providerName, count($configuration), count($configuration) > 1 ? 'values' : 'value'));
            }
        }
    }

    /**
     * Determine if configuration should be updated.
     */
    private function shouldUpdateConfig(): bool
    {
        return $this->isIncluded() && ! $this->isExcluded();
    }

    /**
     * Determine if running console command where config should be updated.
     */
    private function isIncluded(): bool
    {
        $include = config('config-secrets.console.include', []);

        return empty($include) || app()->runningConsoleCommand($include);
    }

    /**
     * Determine if running console command where config should not be updated.
     */
    private function isExcluded(): bool
    {
        $exclude = config('config-secrets.console.exclude', []);

        return app()->runningConsoleCommand($exclude);
    }

    /**
     * Get configuration for the current environment.
     */
    private function getEnvironmentConfig(): array
    {
        return config('config-secrets.environments.'.config('app.env'), []);
    }

    /**
     * Get general options for provider.
     */
    private function getProviderOptions(string $providerName): array
    {
        return config('config-secrets.providers.'.$providerName, []);
    }

    /**
     * Get implementation of provider.
     */
    private function getProvider(string $providerName): string
    {
        $provider = config('config-secrets.providers.'.$providerName.'.provider');

        if (is_null($provider)) {
            throw new Exception('No provider named '.$providerName.' exists');
        }

        return $provider;
    }

    /**
     * Retrieve a new instance of provider from the container.
     */
    private function resolveProvider(string $provider): ConfigProvider
    {
        return app()->make($provider);
    }

    /**
     * Update configuration.
     */
    private function updateConfiguration(array $configuration): void
    {
        config($configuration);
    }
}
