<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use Orchestra\Testbench\TestCase;

class ArrayConfigProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'logging.default'                           => 'stack',
            'config-secrets.providers.array.provider'   => ArrayConfigProvider::class,
            'config-secrets.environments.testing.array' => [
                'logging.default' => 'syslog',
            ],
        ]);
    }

    public function testItOverridesConfiguration()
    {
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('syslog', config('logging.default'));
    }
}
