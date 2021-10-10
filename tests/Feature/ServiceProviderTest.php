<?php

namespace audunru\ConfigSecrets\Tests\Feature;

use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use audunru\ConfigSecrets\Tests\TestCase;
use Exception;
use Illuminate\Support\Arr;
use JsonException;
use Mockery\MockInterface;

class ServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.mysql.password'    => 'original-password',
            'config-secrets.aws.region'              => 'some-region',
            'config-secrets.enabled-environments'    => ['testing'],
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => 'database.connections.mysql.password',
            ],
        ]);
    }

    public function testItOverridesConfiguration()
    {
        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItOverridesConfigurationWithTwoSecrets()
    {
        config([
            'config-secrets.configuration-overrides' => [
                'APP_KEY'     => 'app.key',
                'DB_PASSWORD' => 'database.connections.mysql.password',
            ],
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn'], ['ARN' => 'other-arn']]]);
            $mock->shouldReceive('getSecretValue')->twice()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}'], ['SecretString' => '{"APP_KEY":"some-app-key"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('some-app-key', config('app.key'));
        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItOverridesConfigurationOnlyOnce()
    {
        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItFiltersSecretsByName()
    {
        config([
            'config-secrets.aws.secret-name' => 'some-secret-name',
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->withArgs(function ($arg) {
                return 'name' === Arr::get($arg, 'Filters.0.Key') && 'some-secret-name' === Arr::get($arg, 'Filters.0.Values.0');
            })->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItFiltersSecretsByTagKeyAndValue()
    {
        config([
            'config-secrets.aws.tag-key'   => 'some-tag-key',
            'config-secrets.aws.tag-value' => 'some-tag-value',
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->withArgs(function ($arg) {
                return 'tag-key' === Arr::get($arg, 'Filters.0.Key') && 'some-tag-key' === Arr::get($arg, 'Filters.0.Values.0') &&
                    'tag-value' === Arr::get($arg, 'Filters.1.Key') && 'some-tag-value' === Arr::get($arg, 'Filters.1.Values.0');
            })->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testItCanBeDisabled()
    {
        config([
            'config-secrets.enabled-environments' => ['not-testing'],
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldNotReceive('listSecrets');
            $mock->shouldNotReceive('getSecretValue');
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('original-password', config('database.connections.mysql.password'));
    }

    public function testConfigurationOverridesSupportsArrays()
    {
        config([
            'config-secrets.configuration-overrides' => [
                'DB_PASSWORD' => ['database.connections.mysql.password', 'database.connections.other-mysql.password'],
            ],
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
        $this->assertEquals('secret-password', config('database.connections.other-mysql.password'));
    }

    public function testItRethrowsExceptionInListSecrets()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('some-message');

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andThrow(new Exception('some-message'));
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());
    }

    public function testItRethrowsJsonException()
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Syntax error');

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());
    }

    public function testItRethrowsExceptionInGetSecretValue()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('some-message');

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andThrow(new Exception('some-message'));
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());
    }
}
