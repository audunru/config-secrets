<?php

namespace audunru\ConfigSecrets\Contracts;

interface ConfigProvider
{
    /**
     * Retrieve configuration.
     *
     * @returns array<string, string>
     */
    public function getConfiguration(array $options): array;
}
