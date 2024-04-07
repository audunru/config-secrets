# Migrate from V1 to V2

v2.0.0 brings with it some breaking changes in order to unify how configuration values are set, and possibly support new sources of secrets and configuration values in the future.

## config-secrets.php

When upgrading, you must manually update your configuration to the v2 format. Here's a comparison of the old and new format.

### v1

```php
use audunru\ConfigSecrets\Gateways\AwsSecretsManager;

return [
  'default' => 'aws', // removed in v2
  'enabled-environments' => [  // renamed to 'environments' in v2. You must now also specify which providers to use for each environment.
    'local',
    'production',
  ],
  'aws' => [ // Moved inside a providers section
    'region' => env('AWS_DEFAULT_REGION'),
    'secret-name' => env('AWS_SECRET_NAME', ''),
    'tag-key'   => env('AWS_SECRETS_TAG_KEY', ''),
    'tag-value' => env('AWS_SECRETS_TAG_VALUE', ''),
    'gateway' => AwsSecretsManager::class, // removed in v2
  ],
  'configuration-overrides' => [  // moved inside providers => aws and renamed to 'configuration'
    'APP_KEY' => 'app.key', // The key and its value have switched places in v2, and support for replacing multiple keys with one secret has been dropped
  ],
  'environment-overrides' => [ // replaced by the array config provider in v2
    'local' => [
      'logging.default' => 'stack',
    ],
    'production' => [
      'logging.default' => 'syslog',
    ],
  ],
]

```

### v2

```php
use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigProviders\AwsConfigProvider;

return [
  'providers' => [ // New section
    'aws' => [ // Moved from root
      'provider' => AwsConfigProvider::class, // New option, must be added
      'region' => env('AWS_DEFAULT_REGION'),
      'secret-name' => env('AWS_SECRET_NAME', ''),
      'tag-key'   => env('AWS_SECRETS_TAG_KEY', ''),
      'tag-value' => env('AWS_SECRETS_TAG_VALUE', ''),
      'configuration' => [ // Renamed from configuration-overrides and moved here
        'app.key' => 'APP_KEY' // 'app.key' is the configuration key you want to replace, and APP_KEY is the name of the secret
      ]
    ]
  ],
  'environments' => [ // Renamed from enabled-environments
    'local' => [ // For each environment, you must specify the name of one or more config providers
      'array' => [ // Each provider's environment specific options will override options set in the providers array
        'configuration' => [
          'logging.default' => 'stack',
        ],
      ],
    ],
    'production' => [
      'array' => [
        'configuration' => [
          'logging.default' => 'syslog',
        ],
      ]
      'aws',
    ],
  ],
]

```
