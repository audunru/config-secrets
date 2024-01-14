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

    public function getConfiguration(array $options): array
    {
        $secrets = $this->awsSecretsManager->getSecrets();
        $overrides = Arr::get($options, 'configuration-overrides', []);
        $overridesWithSecrets = array_intersect($overrides, array_keys($secrets->toArray()));

        return array_map(function (string $secretKey) use ($secrets) {
            $value = Arr::get($secrets, $secretKey);

            return $this->getDecodedValue($value);
        }, $overridesWithSecrets);
    }

    private function getDecodedValue(string $value): string
    {
        if (str_starts_with($value, 'base64:')) {
            return base64_decode(substr($value, 7));
        }

        return $value;
    }
}
