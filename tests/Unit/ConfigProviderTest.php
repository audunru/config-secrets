<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use Exception;
use Orchestra\Testbench\TestCase;

class ConfigProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'config-secrets.environments.testing.array' => [
                'logging.default' => 'syslog',
            ],
        ]);
    }

    public function test_it_throws_exception_when_provider_does_not_exist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No provider named array exists');

        ConfigSecretsServiceProvider::updateConfiguration(app());
    }
}
