<?php

namespace audunru\ConfigSecrets\Helpers;

use Illuminate\Support\Collection;

class ConfigurationHelper
{
    /**
     * Get class for the configured default gateway.
     */
    public static function getDefaultGateway(): string
    {
        $defaultGateway = config('config-secrets.default');

        return config(sprintf('config-secrets.%s.gateway', $defaultGateway));
    }

    /**
     * Check if secrets are enabled in this environment.
     */
    public static function isEnabled(): bool
    {
        return in_array(config('app.env'), config('config-secrets.enabled-environments', []));
    }

    /**
     * Update configuration with secrets.
     */
    public static function updateConfiguration(Collection $secrets): void
    {
        $overrides = config('config-secrets.configuration-overrides');

        $secrets->intersectByKeys($overrides)
            ->each(function ($value, $key) use ($overrides) {
                if (str_starts_with($value, 'base64:')) {
                    $value = base64_decode(substr($value, 7));
                }
                self::updateConfigurationValue($key, $value, $overrides);
            });
    }

    /**
     * Update configuration with secret.
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private static function updateConfigurationValue(string $secretKey, string $secretValue, array $overrides): void
    {
        if (is_array($overrides[$secretKey])) {
            foreach ($overrides[$secretKey] as $key) {
                config([$key => $secretValue]);
            }
        } else {
            config([$overrides[$secretKey] => $secretValue]);
        }
    }

    /**
     * Get overrides for current environment.
     */
    private static function getEnvironmentOverrides(): Collection
    {
        $overrides = config('config-secrets.environment-overrides.'.config('app.env'), []);

        return collect($overrides);
    }

    /**
     * Update configuration for current environment.
     */
    public static function updateEnvironmentConfiguration(): void
    {
        $overrides = self::getEnvironmentOverrides();

        collect($overrides)
            ->each(function (string $value, string $key) {
                config([$key => $value]);
            });
    }
}
