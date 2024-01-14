<?php

namespace audunru\ConfigSecrets\Helpers;

use Illuminate\Support\Collection;

class ConfigurationHelper
{
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
}
