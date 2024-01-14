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

    public function testItThrowsExceptionWhenProviderDoesNotExist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No provider named array exists');

        ConfigSecretsServiceProvider::updateConfiguration(app());
    }
}
