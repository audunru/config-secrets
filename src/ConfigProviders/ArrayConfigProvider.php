<?php

namespace audunru\ConfigSecrets\ConfigProviders;

use audunru\ConfigSecrets\Contracts\ConfigProvider;

class ArrayConfigProvider implements ConfigProvider
{
    public function getOverrides(array $options): array
    {
        return $options;
    }
}
