<?php

namespace audunru\ConfigSecrets\ConfigProviders;

use audunru\ConfigSecrets\Contracts\ConfigProvider;
use audunru\ConfigSecrets\Gateways\AwsSecretsManager;
use Illuminate\Support\Arr;

class AwsConfigProvider implements ConfigProvider
{
    public function __construct(private AwsSecretsManager $awsSecretsManager)
    {
    }

    public function getOverrides(array $options): array
    {
        $secrets = $this->awsSecretsManager->getSecrets();

        $overrides = [];

        foreach ($options['configuration-overrides'] as $secretKey => $configKey) {
            if (is_array($configKey)) {
                foreach ($configKey as $key) {
                    $value = Arr::get($secrets, $secretKey);
                    if (str_starts_with($value, 'base64:')) {
                        $value = base64_decode(substr($value, 7));
                    }

                    $overrides[$key] = $value;
                }
            } else {
                $value = Arr::get($secrets, $secretKey);
                if (str_starts_with($value, 'base64:')) {
                    $value = base64_decode(substr($value, 7));
                }

                $overrides[$configKey] = $value;
            }
        }

        return $overrides;
    }
}
