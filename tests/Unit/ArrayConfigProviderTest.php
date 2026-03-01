<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use Illuminate\Support\Facades\Log;
use Mockery;
use Orchestra\Testbench\TestCase;

class ArrayConfigProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'logging.default'                => 'stack',
            'config-secrets.providers.array' => [
                'provider'        => ArrayConfigProvider::class,
                'configuration'   => [
                    'logging.default' => 'syslog',
                ],
            ],
            'config-secrets.environments.testing' => [
                'array',
            ],
        ]);
    }

    public function testItOverridesConfiguration()
    {
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('syslog', config('logging.default'));
    }

    public function testItOverridesConfigurationWithEnvironmentValues()
    {
        config([
            'logging.default'                                             => 'stack',
            'config-secrets.environments.testing.array.configuration'     => [
                'logging.default' => 'papertrail',
            ],
        ]);

        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('papertrail', config('logging.default'));
    }

    public function testItDoesNotLogByDefault()
    {
        Log::spy();

        ConfigSecretsServiceProvider::updateConfiguration(app());

        Log::shouldNotHaveReceived('info');
    }

    public function testItLogsWhenLogIsTrue()
    {
        config(['config-secrets.providers.array.log' => true]);

        Log::spy();

        ConfigSecretsServiceProvider::updateConfiguration(app());

        Log::shouldHaveReceived('info')
            ->once()
            ->with(Mockery::on(fn ($msg) => str_contains($msg, 'array')));
    }

    public function testItLogsWhenLogIsTrueInEnvironmentConfig()
    {
        config([
            'config-secrets.providers.array.log'  => false,
            'config-secrets.environments.testing' => [
                'array' => ['log' => true],
            ],
        ]);

        Log::spy();

        ConfigSecretsServiceProvider::updateConfiguration(app());

        Log::shouldHaveReceived('info')->once();
    }
}
