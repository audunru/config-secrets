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
            'logging.default' => 'stack',
            'config-secrets.providers.array' => [
                'provider' => ArrayConfigProvider::class,
                'configuration' => [
                    'logging.default' => 'syslog',
                ],
            ],
            'config-secrets.environments.testing' => [
                'array',
            ],
        ]);
    }

    public function test_it_overrides_configuration()
    {
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('syslog', config('logging.default'));
    }

    public function test_it_overrides_configuration_with_environment_values()
    {
        config([
            'logging.default' => 'stack',
            'config-secrets.environments.testing.array.configuration' => [
                'logging.default' => 'papertrail',
            ],
        ]);

        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('papertrail', config('logging.default'));
    }

    public function test_it_does_not_log_by_default()
    {
        Log::spy();

        ConfigSecretsServiceProvider::updateConfiguration(app());

        Log::shouldNotHaveReceived('info');
    }

    public function test_it_logs_when_log_is_true()
    {
        config(['config-secrets.providers.array.log' => true]);

        Log::spy();

        ConfigSecretsServiceProvider::updateConfiguration(app());

        Log::shouldHaveReceived('info')
            ->once()
            ->with(Mockery::on(fn ($msg) => str_contains($msg, 'array')));
    }

    public function test_it_logs_when_log_is_true_in_environment_config()
    {
        config([
            'config-secrets.providers.array.log' => false,
            'config-secrets.environments.testing' => [
                'array' => ['log' => true],
            ],
        ]);

        Log::spy();

        ConfigSecretsServiceProvider::updateConfiguration(app());

        Log::shouldHaveReceived('info')->once();
    }
}
