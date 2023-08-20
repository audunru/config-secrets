<?php

use audunru\ConfigSecrets\Gateways\AwsSecretsManager;

return [
    /*
    |--------------------------------------------------------------------------
    | Default secret store
    |--------------------------------------------------------------------------
    |
    | This option controls which 3rd party secrets are retrieved from.
    |
    | Supported: "aws"
    |
    */
    'default' => 'aws',

    /*
    |--------------------------------------------------------------------------
    | Environments where this service provider is enabled
    |--------------------------------------------------------------------------
    |
    | List the environment names in an array where this package should be enabled, it will be compared against env('APP_ENV').
    |
    */

    'enabled-environments' => [
        'local',
        'production',
    ],

    'aws' => [
        /*
        |--------------------------------------------------------------------------
        | AWS Region where secrets are stored
        |--------------------------------------------------------------------------
        |
        | The AWS Region where secrets are stored.
        |
        */

        'region' => env('AWS_DEFAULT_REGION'),

        /*
        |--------------------------------------------------------------------------
        | Secret name
        |--------------------------------------------------------------------------
        |
        | Only the secret with this name will be retrieved. Leave empty to retrieve all secrets.
        |
        */

        'secret-name'      => env('AWS_SECRET_NAME', ''),

        /*
        |--------------------------------------------------------------------------
        | Tag used to return list of secrets
        |--------------------------------------------------------------------------
        |
        | Only secrets tagged with this key/value will be retrieved. Leave empty to retrieve all secrets.
        |
        */

        'tag-key'   => env('AWS_SECRETS_TAG_KEY', ''),
        'tag-value' => env('AWS_SECRETS_TAG_VALUE', ''),

        /*
        |--------------------------------------------------------------------------
        | Gateway
        |--------------------------------------------------------------------------
        |
        | Class that retrieves secrets from 3rd party service.
        |
        */

        'gateway' => AwsSecretsManager::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Override configuration with secrets
    |--------------------------------------------------------------------------
    |
    | Secrets will override existing configuration variables if they are listed here
    |
    */
    'configuration-overrides' => [
        // A secret with this name will override this configuration variable ("dot notation" is used)
        // 'APP_KEY'        => 'app.key',
        // 'DB_PASSWORD'    => 'database.connections.mysql.password',

        // A single secret can override multiple configuration values if an array of configuration variables is used.
        // 'REDIS_PASSWORD' => ['database.redis.default.password', 'database.redis.cache.password'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment overrides
    |--------------------------------------------------------------------------
    |
    | Override configuration values in specific environments.
    |
    | Do not use this for secrets!
    |
    */
    'environment-overrides' => [
        // logging.default will be set to 'stack' in the local (development) environment, and to 'syslog' in production
        // 'local' => [
        //     'logging.default' => 'stack',
        // ],
        // 'production' => [
        //     'logging.default' => 'syslog',
        // ],
    ],
];
