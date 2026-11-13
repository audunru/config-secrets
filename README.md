# Configuration secrets for Laravel

[![Build Status](https://github.com/audunru/config-secrets/actions/workflows/validate.yml/badge.svg)](https://github.com/audunru/config-secrets/actions/workflows/validate.yml)
[![Coverage Status](https://coveralls.io/repos/github/audunru/config-secrets/badge.svg?branch=main)](https://coveralls.io/github/audunru/config-secrets?branch=main)

Retrieve secrets from AWS Secrets Manager and override config variables in Laravel.

As an example, you could store your database password in AWS Secrets Manager instead of your .env file. This package does not modify your .env file or config files. Instead, the configuration values are set using Laravel's `config()` helper right after your application has started.

# Migration guides

- [v1 to v2](docs/v1-v2-migration.md)
- [v2 to v3](docs/v2-v3-migration.md)

# Installation

## Step 1: Install with Composer

```bash
composer require audunru/config-secrets
```

## Step 2: Publish configuration

Publish the configuration file by running:

```php
php artisan vendor:publish --tag=config-secrets-config
```

## Step 3: Edit configuration

This package supports two config providers: `aws` retrieves secrets from AWS Secrets Manager, and the `array` provider simply retrieves them from [config-secrets.php](config/config-secrets.php).

### Aws

In [AWS Secrets Manager](https://aws.amazon.com/secrets-manager/):

1. Create a new secret.
2. Set the secret value to any number of key/value pairs. You can prefix the secret value with `base64:` followed by a base64 encoded string. This is useful for private and public keys, for instance.

In your Laravel application:

1. Set `AWS_DEFAULT_REGION` in `.env` or set the region directly in [config-secrets.php](config/config-secrets.php)
2. Set `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` in `.env` or [use any of the other options that AWS SDK offers](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html)
3. Set `AWS_SECRET_NAME`, or both `AWS_SECRETS_TAG_KEY` and `AWS_SECRETS_TAG_VALUE`, in `.env` (see [Filter the secrets](#filter-the-secrets)), or set the filter per environment (see [Set the options per environment](#set-the-options-per-environment))
4. Map Laravel configuration keys to secret keys under the `aws` provider's `configuration` section in [config-secrets.php](config/config-secrets.php)

#### Filter the secrets

Always set a filter. Without a filter, the package reads all secrets in the AWS account (up to 100) and merges their key/value pairs. When two secrets have the same key, for example `APP_KEY`, the value from the secret that AWS returns last is used. In an account that holds secrets for more than one application or environment, your application can thus get the values of a different application or environment.

AWS matches the `name`, `tag-key` and `tag-value` filters as prefixes, not as exact values. For example, `AWS_SECRET_NAME=myapp` also matches a secret named `myapp-staging`. The tag key and the tag value are two separate filters, so they do not have to match the same tag. See the [AWS `Filter` documentation](https://docs.aws.amazon.com/secretsmanager/latest/apireference/API_Filter.html).

#### Set the options per environment

You can set `region`, `secret-name`, `tag-key` and `tag-value` for each environment under `environments.<env>.aws` in [config-secrets.php](config/config-secrets.php). The environment options override the options under `providers.aws`. Thus the secret name of each environment is in source control, and a server that does not set `AWS_SECRET_NAME` cannot read the secrets of a different environment.

```php
'environments' => [
    'staging' => [
        'aws' => ['secret-name' => 'my_app_staging'],
    ],
    'production' => [
        'aws' => ['secret-name' => 'my_app_production'],
    ],
],
```

## Array

The array provider replaces configuration values with values from [config-secrets.php](config/config-secrets.php). Look in [config-secrets.php](config/config-secrets.php) for an example. This allows you to keep environment specific configuration values in source control. For obvious reasons, do not use the array provider for values that should be kept secret.

## Step 4: Update bootstrap/app.php

Add the following lines to `bootstrap/app.php` (recommended but not required):

```php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use audunru\ConfigSecrets\ConfigSecretsServiceProvider;

$app->afterBootstrapping(LoadConfiguration::class, fn (Application $app) => ConfigSecretsServiceProvider::registerAndUpdate($app));
```

Loading the secrets in `bootstrap/app.php` instead of in a service provider ensures that you can override (probably) any configuration value. For instance, Laravel's `RedisServiceProvider` uses the available configuration values when it is registered. Without the code above, you won't be able to override the Redis password.

## Step 5: Enable configuration cache

It is _very important_ that you cache your Laravel configuration with `php artisan config:cache` or `php artisan optimize` when you use this package. If not, secrets will be retrieved for every request. This process is slow and costly!

# Alternatives

[AWS Secrets Manager](https://github.com/TappNetwork/laravel-aws-secrets-manager)

# Development

## Testing

Run tests:

```bash
composer verify
```
