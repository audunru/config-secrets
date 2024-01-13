# Configuration secrets for Laravel

[![Build Status](https://github.com/audunru/config-secrets/actions/workflows/validate.yml/badge.svg)](https://github.com/audunru/config-secrets/actions/workflows/validate.yml)
[![Coverage Status](https://coveralls.io/repos/github/audunru/config-secrets/badge.svg?branch=master)](https://coveralls.io/github/audunru/config-secrets?branch=master)
[![StyleCI](https://github.styleci.io/repos/415400658/shield?branch=master)](https://github.styleci.io/repos/415400658)

Retrieve secrets from AWS Secrets Manager and override config variables in Laravel.

As an example, you could store your database password in AWS Secrets Manager instead of your .env file. This package does not modify your .env file or config files. Instead, the configuration values are set using Laravel's `config()` helper right after your application has started.

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

Currently this package supports retrieving secrets from AWS Secrets Manager.

In AWS Secrets Manager:

1. Create a new secret.
2. Set the secret value to any number of key/value pairs. You can prefix the secret value with `base64:` followed by a base64 encoded string. This is useful for private and public keys, for instance.

In your Laravel application:

1. Set `AWS_DEFAULT_REGION` in `.env` or set the region directly in `config-secrets.php`
2. Set `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` in `.env` or [use any of the other options that AWS SDK offers](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html)
3. Map secret keys to Laravel configuration keys under `configuration-overrides` in `config-secrets.php`

This package also supports overriding configuration values per environment. Look in `config-secrets.php` for an example. This allows you to keep environment specific configuration values in source control. For obvious reasons, do not use environment overrides for values that should be kept secret.

## Step 4: Update bootstrap/app.php

Add the following lines to `bootstrap/app.php` (recommended but not required):

```php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use audunru\ConfigSecrets\ConfigSecretsServiceProvider;

$app->afterBootstrapping(LoadConfiguration::class, fn (Application $app) => ConfigSecretsServiceProvider::registerAndUpdate($app));
```

Loading the secrets in `bootstrap/app.php` instead of in a service provider ensures that you can override (probably) any configuration value. If you do not do this, you will not be able to override config values that are used by service providers that run before this package's own service provider. For instance, Laravel's `RedisServiceProvider` uses the available configuration values when it is registered. Without the code above, you won't be able to override the Redis password.

## Step 4: Enable configuration cache

It is _very important_ that you cache your Laravel configuration with `php artisan config:cache` or `php artisan optimize` when you use this package. If not, secrets will be retrieved for every request. This process is slow and costly!

# Command line

Run `php artisan config:cache` or `php artisan optimize` to update your cached configuration with new secret values.

# Alternatives

[AWS Secrets Manager](https://github.com/TappNetwork/laravel-aws-secrets-manager)

# Development

## Testing

Run tests:

```bash
composer verify
```
