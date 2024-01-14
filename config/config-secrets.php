<?php

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigProviders\AwsConfigProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Options for configuration override providers
    */

    'providers' => [
        'array' => [
            'provider' => ArrayConfigProvider::class,
            // A configuration key ("dot notation" is used) will have its value replaced
            // 'logging.default' => 'stack'
        ],
        'aws'   => [
            'provider' => AwsConfigProvider::class,
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
            | Override configuration with secrets
            |--------------------------------------------------------------------------
            |
            | Secrets will override existing configuration variables if they are listed here.
            |
            */

            'configuration-overrides' => [
                // A configuration key ("dot notation" is used) will have its value replaced by a secret
                // 'app.key'                             => 'APP_KEY',
                // 'database.connections.mysql.password' => 'DB_PASSWORD',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment overrides
    |--------------------------------------------------------------------------
    |
    | Specify which providers are used in which environments
    */

    'environments' => [
        // 'local' => [
        //     'array',
        // ],
        // 'production' => [
        //     'array',
        //     'aws',
        // ],
    ],
];
