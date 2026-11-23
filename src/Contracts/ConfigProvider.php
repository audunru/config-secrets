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
     * The result is kept in memory for the current PHP process, keyed on the
     * provider name and $options, so it must depend only on $options.
     *
     * @returns array<string, string>
     */
    public function getConfiguration(array $options): array;
}
