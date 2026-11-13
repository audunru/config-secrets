# config-secrets

Overrides Laravel config values at bootstrap with values from config providers, for example secrets from AWS Secrets Manager. Each environment chooses which providers to use.

## Public API

- **`ConfigSecretsServiceProvider`** (`src/ConfigSecretsServiceProvider.php`) — static entry points for the consumer's `bootstrap/app.php`:
  - `registerAndUpdate(Application $app)` — registers the dependencies and updates the config. The README tells consumers to call it with `$app->afterBootstrapping(LoadConfiguration::class, ...)`, so secrets are in place before other service providers register
  - `registerDependencies(Application $app)` — binds `UpdateConfiguration` as a singleton (no-op if already bound)
  - `updateConfiguration(Application $app)` — runs `UpdateConfiguration` once; skipped when the config is cached or `UpdateConfiguration` was already resolved
- **`ConfigProvider` contract** (`src/Contracts/ConfigProvider.php`) — `getConfiguration(array $options): array` returns `['dot.config.key' => value]`. Implement it to add a new source
- **Built-in providers** (`src/ConfigProviders/`):
  - `ArrayConfigProvider` — returns the `configuration` option as it is
  - `AwsConfigProvider` — gives `region`, `secret-name`, `tag-key` and `tag-value` from the merged options to the gateway; maps config keys to secret names (`'app.key' => 'APP_KEY'`); only keys whose secret exists are returned; values prefixed `base64:` are decoded
- **`AwsSecretsManager` gateway** (`src/Gateways/AwsSecretsManager.php`) — `getSecrets($region, $secretName, $tagKey, $tagValue)` lists up to 100 secrets (filtered by the secret name and/or the tag key + tag value), and decodes each `SecretString` as a flat JSON object (depth 2). Errors are logged as alerts and rethrown
- **`UpdateConfiguration` service** (`src/Services/UpdateConfiguration.php`) — reads `environments.<app.env>`, merges per-environment options over `providers.<name>` with `array_replace_recursive()`, resolves each provider from the container and writes the result with `config()`

## Service Provider

- Config key: `config-secrets` (publish with `--tag=config-secrets-config`)
- `packageRegistered()` only calls `registerDependencies()`. It does **not** update the config; the consumer must call `registerAndUpdate()` or `updateConfiguration()`
- Config sections:
  - `console.include` / `console.exclude` — artisan commands for which the update runs / does not run (empty `include` = all)
  - `providers.<name>` — `provider` class, `log` (log how many values the provider supplied), plus provider options (`configuration`; for `aws` also `region`, `secret-name`, `tag-key`, `tag-value`)
  - `environments.<env>` — list of provider names, or `name => [options]` to override options for that environment

## Dependencies

- External: `aws/aws-sdk-php` (`SecretsManagerClient`, API version `2017-10-17`)

## Testing

Tests in `tests/Unit/` extend `Orchestra\Testbench\TestCase` directly (there is no package `TestCase`). They set `config-secrets.*` with `config([...])` in `setUp()` and call `ConfigSecretsServiceProvider::updateConfiguration(app())`. AWS is mocked with `$this->mock('overload:Aws\SecretsManager\SecretsManagerClient', ...)`.

## Conventions / Gotchas

- The environment `testing` is used in tests, so test config goes under `config-secrets.environments.testing`.
- Consumers must cache config (`php artisan config:cache`); otherwise AWS is called on every request. `updateConfiguration()` does nothing when the config is cached.
- An environment entry that names a provider that is not in `providers` throws `Exception('No provider named ... exists')`.
- `updateConfiguration()` runs only once per app instance (singleton guard). See `test_it_overrides_configuration_only_once` in `tests/Unit/AwsConfigProviderTest.php`.
- `AwsSecretsManager` reads no config. It gets its options from `AwsConfigProvider`, so environment options (`environments.<env>.aws.region` etc.) override the provider options.
- Pagination of `listSecrets` is not implemented; only the first 100 secrets are used.
- The `base64:` prefix is for multiline values such as PEM keys (see `test_configuration_overrides_supports_base64`).
- `docs/v1-v2-migration.md` documents the v1 → v2 config format change.
- `docs/v2-v3-migration.md` documents the v2 → v3 change: per-environment AWS options and the new `getSecrets()` signature.
