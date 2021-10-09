<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\Helpers\SecretsHelper;
use audunru\ConfigSecrets\Tests\TestCase;

class SecretsHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.mysql.password'    => 'original-password',
            'config-secrets.enabled-environments'    => ['testing'],
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => 'database.connections.mysql.password',
            ],
        ]);
    }

    public function testItUpdatesConfigurationValue()
    {
        SecretsHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItDoesNotUpdateConfigurationValueWhenKeyDoesNotExistInConfigurationOverrides()
    {
        config([
            'config-secrets.configuration-overrides' => [
                'NOT_DB_PASSWORD' => 'database.connections.mysql.password',
            ],
        ]);

        SecretsHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('original-password', config('database.connections.mysql.password'));
    }

    public function testItDoesNotUpdateConfigurationValueWhenConfigurationOverridesIsEmptyArray()
    {
        config([
            'config-secrets.configuration-overrides' => [],
        ]);

        SecretsHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('original-password', config('database.connections.mysql.password'));
    }

    public function testConfigurationOverridesSupportsArrays()
    {
        config([
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => ['database.connections.mysql.password', 'database.connections.other-mysql.password'],
            ],
        ]);

        SecretsHelper::updateConfiguration(collect(['DB_PASSWORD' => 'secret-password']));

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
        $this->assertEquals('secret-password', config('database.connections.other-mysql.password'));
    }
}
