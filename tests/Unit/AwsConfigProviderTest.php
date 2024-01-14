<?php

namespace audunru\ConfigSecrets\Tests\Unit;

use audunru\ConfigSecrets\ConfigProviders\AwsConfigProvider;
use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use Exception;
use Illuminate\Support\Arr;
use JsonException;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;

class AwsConfigProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.mysql.password'                  => 'original-password',
            'config-secrets.providers.aws'                         => [
                'provider'                => AwsConfigProvider::class,
                'configuration-overrides' => [
                    'database.connections.mysql.password' => 'DB_PASSWORD',
                ],
            ],
            'config-secrets.environments.testing' => [
                'aws',
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

    public function testItOverridesConfigurationWithEnvironmentValues()
    {
        config([
            'database.connections.mysql.password'                     => 'original-password',
            'config-secrets.environments.testing.aws'                 => [
                'configuration-overrides' => [
                    'database.connections.mysql.password' => 'DATABASE_PASSWORD',
                ],
            ],
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DATABASE_PASSWORD":"supersecret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('supersecret-password', config('database.connections.mysql.password'));
    }

    public function testItOverridesConfigurationWithTwoSecrets()
    {
        config([
            'config-secrets.providers.aws.configuration-overrides' => [
                'app.key'                             => 'APP_KEY',
                'database.connections.mysql.password' => 'DB_PASSWORD',
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

    public function testItOverridesOnlyIfSecretExists()
    {
        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD2":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('original-password', config('database.connections.mysql.password'));
    }

    public function testItFiltersSecretsByName()
    {
        config([
            'config-secrets.providers.aws.secret-name' => 'some-secret-name',
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
            'config-secrets.providers.aws.tag-key'   => 'some-tag-key',
            'config-secrets.providers.aws.tag-value' => 'some-tag-value',
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->withArgs(function ($arg) {
                return 'tag-key' === Arr::get($arg, 'Filters.0.Key') && 'some-tag-key' === Arr::get($arg, 'Filters.0.Values.0')
                    && 'tag-value' === Arr::get($arg, 'Filters.1.Key') && 'some-tag-value' === Arr::get($arg, 'Filters.1.Values.0');
            })->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"DB_PASSWORD":"secret-password"}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('secret-password', config('database.connections.mysql.password'));
    }

    public function testConfigurationOverridesSupportsBase64()
    {
        config([
            'config-secrets.providers.aws.configuration-overrides' => [
                'passport.public_key' => 'PASSPORT_PUBLIC_KEY',
            ],
        ]);

        $this->mock('overload:Aws\SecretsManager\SecretsManagerClient', function (MockInterface $mock) {
            $mock->shouldReceive('listSecrets')->once()->andReturn(['SecretList' => [['ARN' => 'example-arn']]]);
            $mock->shouldReceive('getSecretValue')->once()->andReturn(['SecretString' => '{"PASSPORT_PUBLIC_KEY":"base64:LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlJQ0lqQU5CZ2txaGtpRzl3MEJBUUVGQUFPQ0FnOEFNSUlDQ2dLQ0FnRUExSVkrcDZQckNpVk83QllTU2hUdApvYnRaYzI0cmczemNVZElGbTB5MFJ6RC85VWNNZzl1M1AyUEZBMWI5MTJqZDRReXRoU3VxMm9xbTBTekozU2dOCjZQVkltQlh1WXBWdkx0bUp1ODRQTG9Bd1FRQ0hUYWJvTWJGa3Q0RmZseWVqemhObG1aWWR4N2l1UmNlV2N2QjAKWit4R0dKNlZkQnVlS2dsZGNsWVdzOExIY0taSng3ajR3d21pSGZSRjFpUWR5ZTlMcnFEdFhHZ05GZktWRW5SZwpVdnRPQVhYQmFNZzVabVRkM1hVcUdEYnpvdVFwZWRrcmwyMkt2TlhvS1VqNGEraTJOOVRSYVU3SnRURUVzSDZECkY4QjM4Z1gwQjZlYW4rN21zL1YzMTF2Nm9TUlY0Zm5Kdk9lTmRLODg3cEs1dWZGVUZyckpvWWsrdC83WXNlSDUKR3VxaS9kZ2J1TnpRRitPQ0RqQitnNEh5Nnk1aVcrTDk0Q201WCswYkNwTllpRzRqQXVKSW5PNXhsNERRZytoNQpFSzdEclJLUkxTa3V1UnJHc1ZpeDBNdmtpcUZxL2Z2enc1SnFMeThLZG92bEUrSzFza21tcFdYV214QmVKU2xXCkNUUHJNcHprdkFrNVNPbFlLZ0Nyc1JGbUxsMVBvS0ZOOUE0YnBFZ2RhWWsxMFpyVW9WeHBTNHkvZ0U1T2VZUXYKcDFFR3piWE84c2l6cUtJbEZmNHpHa3JoVnNTaHp1dk50QXpJU2JTQ3JxcE9aTjk3N29STkRhYkpOM0JzYTY5awpFQjF0cG1aRmFlZ2dGem9EOHBWYm5yL3Qwd0YvTndmRGFtbVhsZEZFbi8vd05iZjJmTjZjNWlrQ3RGaXRYVFpjCmQ3blBNTytUbmJLdExDM3drdjR5Y0pFQ0F3RUFBUT09Ci0tLS0tRU5EIFBVQkxJQyBLRVktLS0tLQ=="}']);
        });
        ConfigSecretsServiceProvider::updateConfiguration(app());

        $this->assertEquals('-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA1IY+p6PrCiVO7BYSShTt
obtZc24rg3zcUdIFm0y0RzD/9UcMg9u3P2PFA1b912jd4QythSuq2oqm0SzJ3SgN
6PVImBXuYpVvLtmJu84PLoAwQQCHTaboMbFkt4FflyejzhNlmZYdx7iuRceWcvB0
Z+xGGJ6VdBueKgldclYWs8LHcKZJx7j4wwmiHfRF1iQdye9LrqDtXGgNFfKVEnRg
UvtOAXXBaMg5ZmTd3XUqGDbzouQpedkrl22KvNXoKUj4a+i2N9TRaU7JtTEEsH6D
F8B38gX0B6ean+7ms/V311v6oSRV4fnJvOeNdK887pK5ufFUFrrJoYk+t/7YseH5
Guqi/dgbuNzQF+OCDjB+g4Hy6y5iW+L94Cm5X+0bCpNYiG4jAuJInO5xl4DQg+h5
EK7DrRKRLSkuuRrGsVix0MvkiqFq/fvzw5JqLy8KdovlE+K1skmmpWXWmxBeJSlW
CTPrMpzkvAk5SOlYKgCrsRFmLl1PoKFN9A4bpEgdaYk10ZrUoVxpS4y/gE5OeYQv
p1EGzbXO8sizqKIlFf4zGkrhVsShzuvNtAzISbSCrqpOZN977oRNDabJN3Bsa69k
EB1tpmZFaeggFzoD8pVbnr/t0wF/NwfDammXldFEn//wNbf2fN6c5ikCtFitXTZc
d7nPMO+TnbKtLC3wkv4ycJECAwEAAQ==
-----END PUBLIC KEY-----', config('passport.public_key'));
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
