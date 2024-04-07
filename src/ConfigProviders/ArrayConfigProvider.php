<?php

namespace audunru\ConfigSecrets\ConfigProviders;

use audunru\ConfigSecrets\Contracts\ConfigProvider;
use Illuminate\Support\Arr;

class ArrayConfigProvider implements ConfigProvider
{
    public function getConfiguration(array $options): array
    {
        return Arr::get($options, 'configuration', []);
    }
}
