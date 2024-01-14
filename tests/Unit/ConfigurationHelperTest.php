<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\Helpers\ConfigurationHelper;
use Orchestra\Testbench\TestCase;

class ConfigurationHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.mysql.password'    => 'original-password',
            'logging.default'                        => 'stack',
            'config-secrets.default'                 => 'aws',
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => 'database.connections.mysql.password',
            ],
            'config-secrets.environment-overrides' => [
                'testing' => [
                    'logging.default' => 'syslog',
                ],
            ],
        ]);
    }

    public function testItUpdatesConfigurationValue()
    {
        ConfigurationHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItDoesNotUpdateConfigurationValueWhenKeyDoesNotExistInConfigurationOverrides()
    {
        config([
            'config-secrets.configuration-overrides' => [
                'NOT_DB_PASSWORD' => 'database.connections.mysql.password',
            ],
        ]);

        ConfigurationHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('original-password', config('database.connections.mysql.password'));
    }

    public function testItDoesNotUpdateConfigurationValueWhenConfigurationOverridesIsEmptyArray()
    {
        config([
            'config-secrets.configuration-overrides' => [],
        ]);

        ConfigurationHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('original-password', config('database.connections.mysql.password'));
    }

    public function testConfigurationOverridesSupportsArrays()
    {
        config([
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => ['database.connections.mysql.password', 'database.connections.other-mysql.password'],
            ],
        ]);

        ConfigurationHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
        $this->assertEquals('secret-password', config('database.connections.other-mysql.password'));
    }
}
