<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\Gateways\AwsSecretsManager;
use audunru\ConfigSecrets\Helpers\ConfigurationHelper;
use audunru\ConfigSecrets\Tests\TestCase;

class ConfigurationHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.mysql.password'    => 'original-password',
            'config-secrets.default'                 => 'aws',
            'config-secrets.aws.gateway'             => AwsSecretsManager::class,
            'config-secrets.enabled-environments'    => ['testing'],
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => 'database.connections.mysql.password',
            ],
        ]);
    }

    public function testItGetsGateway()
    {
        $gateway = ConfigurationHelper::getDefaultGateway();

        $this->assertEquals('audunru\ConfigSecrets\Gateways\AwsSecretsManager', $gateway);
    }

    public function testItIsEnabled()
    {
        $this->assertTrue(ConfigurationHelper::isEnabled());
    }

    public function testItIsDisabled()
    {
        config([
            'config-secrets.enabled-environments'    => ['not-testing'],
        ]);

        $this->assertFalse(ConfigurationHelper::isEnabled());
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
