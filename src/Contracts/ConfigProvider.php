<?php

namespace audunru\ConfigSecrets\Contracts;

interface ConfigProvider
{
    /**
     * Retrieve configuration overrides.
     *
     * @returns array<string, string>
     */
    public function getOverrides(array $options): array;
}
