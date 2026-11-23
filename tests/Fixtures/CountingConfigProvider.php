<?php

namespace audunru\ConfigSecrets\Tests\Fixtures;

use audunru\ConfigSecrets\Contracts\ConfigProvider;
use Exception;

class CountingConfigProvider implements ConfigProvider
{
    public static int $calls = 0;

    public static bool $throw = false;

    public function getConfiguration(array $options): array
    {
        self::$calls++;

        if (self::$throw) {
            throw new Exception('fetch failed');
        }

        return $options['configuration'] ?? [];
    }
}
