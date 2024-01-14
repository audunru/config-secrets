<?php

namespace audunru\ConfigSecrets\ConfigProviders;

use audunru\ConfigSecrets\Contracts\ConfigProvider;

class ArrayConfigProvider implements ConfigProvider
{
    public function getConfiguration(array $options): array
    {
        return $options;
    }
}
