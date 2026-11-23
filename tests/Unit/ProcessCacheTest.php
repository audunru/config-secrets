<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use audunru\ConfigSecrets\Services\UpdateConfiguration;
use audunru\ConfigSecrets\Tests\Fixtures\CountingConfigProvider;
use Exception;
use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;

class ProcessCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        UpdateConfiguration::flush();
        CountingConfigProvider::$calls = 0;
        CountingConfigProvider::$throw = false;
    }

    private function configure(array $environment): void
    {
        config([
            'config-secrets.providers.counting' => [
                'provider' => CountingConfigProvider::class,
                'log' => true,
                'configuration' => ['app.name' => 'From provider'],
            ],
            'config-secrets.environments.testing' => $environment,
        ]);
    }

    private function update(): void
    {
        ConfigSecretsServiceProvider::updateConfiguration(app());
    }

    public function test_it_fetches_one_time_for_two_applications()
    {
        $this->configure(['counting']);
        $this->update();

        $this->refreshApplication();
        Log::spy();
        $this->configure(['counting']);
        $this->update();

        $this->assertSame(1, CountingConfigProvider::$calls);
        $this->assertSame('From provider', config('app.name'));
        Log::shouldNotHaveReceived('info');
    }

    public function test_it_fetches_again_when_options_change()
    {
        $this->configure(['counting']);
        $this->update();

        $this->refreshApplication();
        $this->configure(['counting' => ['configuration' => ['app.name' => 'Other']]]);
        $this->update();

        $this->assertSame(2, CountingConfigProvider::$calls);
        $this->assertSame('Other', config('app.name'));
    }

    public function test_it_caches_each_provider_separately()
    {
        $this->configure(['counting', 'array']);
        config(['config-secrets.providers.array' => ['provider' => ArrayConfigProvider::class, 'configuration' => ['app.locale' => 'nb']]]);
        $this->update();

        $this->refreshApplication();
        $this->configure(['counting', 'array']);
        config(['config-secrets.providers.array' => ['provider' => ArrayConfigProvider::class, 'configuration' => ['app.locale' => 'nb']]]);
        $this->update();

        $this->assertSame(1, CountingConfigProvider::$calls);
        $this->assertSame('From provider', config('app.name'));
        $this->assertSame('nb', config('app.locale'));
    }

    public function test_it_does_not_cache_a_failed_fetch()
    {
        CountingConfigProvider::$throw = true;
        $this->configure(['counting']);

        try {
            $this->update();
        } catch (Exception) {
            // The first fetch fails.
        }

        CountingConfigProvider::$throw = false;
        $this->refreshApplication();
        $this->configure(['counting']);
        $this->update();

        $this->assertSame(2, CountingConfigProvider::$calls);
        $this->assertSame('From provider', config('app.name'));
    }

    public function test_it_caches_an_empty_result()
    {
        $this->configure(['counting']);
        config(['config-secrets.providers.counting.configuration' => []]);
        $this->update();

        $this->refreshApplication();
        $this->configure(['counting']);
        config(['config-secrets.providers.counting.configuration' => []]);
        $this->update();

        $this->assertSame(1, CountingConfigProvider::$calls);
    }

    public function test_flush_clears_the_cache()
    {
        $this->configure(['counting']);
        $this->update();

        $this->refreshApplication();
        UpdateConfiguration::flush();
        $this->configure(['counting']);
        $this->update();

        $this->assertSame(2, CountingConfigProvider::$calls);
    }
}
