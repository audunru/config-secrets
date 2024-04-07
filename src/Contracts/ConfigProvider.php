<?php

namespace audunru\ConfigSecrets\Contracts;

interface ConfigProvider
{
    /**
     * Retrieve configuration.
     *
     * The configuration array will be passed to the config() helper.
     * The array keys can use dot notation.
     *
     * @returns array<string, string>
     */
    public function getConfiguration(array $options): array;
}
