# Migrate from V7 to V8

v8.0.0 fixes a bug: the AWS options `region`, `secret-name`, `tag-key` and `tag-value` in the `environments` section had no effect. The format of `config-secrets.php` does not change.

## Check your environments section

In v7, `AwsSecretsManager` read only the global `config-secrets.providers.aws.*` options. In v8, `AwsConfigProvider` gives the merged options to the gateway. Environment options override the options in the `providers` section.

If your `environments` section sets `region`, `secret-name`, `tag-key` or `tag-value` for the `aws` provider, v8 uses these values. Make sure that they are correct before you upgrade.

## Providers with a different name

If a provider in the `providers` section uses `AwsConfigProvider` and has a name that is not `aws`, v7 used the options of `providers.aws`. v8 uses the options of that provider. Set `region` and the filters in that provider.

## AwsSecretsManager

Only code that uses `AwsSecretsManager` directly must change:

- The constructor does not read the configuration.
- `getSecrets()` takes the region and the filters as arguments:

```php
$secrets = app(AwsSecretsManager::class)->getSecrets(
    region: 'eu-north-1',
    secretName: 'my_app_production',
    tagKey: null,
    tagValue: null,
);
```
