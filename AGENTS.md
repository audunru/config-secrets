# config-secrets

Fetches secrets from external sources (AWS Secrets Manager or a static array) at boot time and overwrites Laravel config values, so apps can use secret-store values without committing them to `.env`.

## Public API

No facade. The entry point consumers use is the service provider itself:

- `ConfigSecretsServiceProvider::registerAndUpdate(Application $app)` — registers the singleton and runs the update in one call; useful for bootstrapping outside the normal provider lifecycle.
- `ConfigSecretsServiceProvider::updateConfiguration(Application $app)` — runs the update only (skipped if config is cached or already resolved).

Custom providers must implement `audunru\ConfigSecrets\Contracts\ConfigProvider` (single method: `getConfiguration(array $options): array`).

## Service Provider

Config key: `config-secrets` (published from `config/config-secrets.php`).

Registers `UpdateConfiguration` as a singleton. The update runs automatically on `packageRegistered` unless the config is cached (`config:cache` bypasses it entirely — this is intentional).

Built-in providers registered via config, not the container:
- `ArrayConfigProvider` — returns a static key/value array from config; no external calls.
- `AwsConfigProvider` — calls AWS Secrets Manager via `AwsSecretsManager` gateway; constructs its own `SecretsManagerClient` from config values at instantiation time.

## Config Structure

`config-secrets.providers.<name>` — provider class + options (region, secret-name, tag-key/value, configuration map, log flag).

`config-secrets.environments.<app.env>` — list of provider names (or name + options) active in that environment. Only providers listed here run; options here override provider-level options via `array_replace_recursive`.

`config-secrets.console.include` / `.exclude` — limit or skip updates when running specific Artisan commands.

## Dependencies

- External: `aws/aws-sdk-php ^3.0`

## Testing

```
composer test
```

Tests are all under `tests/Unit/`. AWS calls are mocked with Mockery's `overload:` prefix to intercept `SecretsManagerClient` construction. Tests call `ConfigSecretsServiceProvider::updateConfiguration(app())` directly rather than booting the provider.

## Conventions / Gotchas

- `updateConfiguration` is a no-op if the app has already resolved `UpdateConfiguration` (singleton guard). Tests that need multiple update calls in one process must work around this — see `test_it_overrides_configuration_only_once` in `tests/Unit/AwsConfigProviderTest.php`.
- Config caching (`php artisan config:cache`) disables secret injection entirely. The check `$app->configurationIsCached()` in `UpdateConfiguration` returns early. This means secrets must be baked into the cache separately or the cache must be skipped.
- `AwsSecretsManager` reads config at construction time, not lazily. Changing `config-secrets.providers.aws.*` after the gateway is instantiated has no effect.
- Secret values prefixed with `base64:` are automatically decoded by `AwsConfigProvider` (used for multiline values like PEM keys — see `test_configuration_overrides_supports_base64`).
- `AwsSecretsManager` fetches up to 100 secrets (`MAX_SECRETS`) per call; pagination is not implemented.
