<?php

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigProviders\AwsConfigProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Options for configuration providers
    */

    'providers' => [
        'array' => [
            /*
            |--------------------------------------------------------------------------
            | Config provider
            |--------------------------------------------------------------------------
            |
            | Receives options and returns a list of config keys and values.
            |
            */

            'provider'      => ArrayConfigProvider::class,

            /*
            |--------------------------------------------------------------------------
            | Configuration
            |--------------------------------------------------------------------------
            |
            | List of key/value pairs that will override the configuration in all
            | environments.
            |
            */

            'configuration' => [
                // A configuration key ("dot notation" is used) will have its value replaced
                // with whatever is specified here.
                // 'logging.default' => 'stack'
            ],
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

            'configuration' => [
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
    | Specify which providers are used in which environments. Environment
    | specific provider options will override generic options set in the
    | providers section.
    */

    'environments' => [
        // 'local' => [
        //     'array' => [
        //         'configuration' => [
        //           'logging.default' => 'stack'
        //         ],
        //     ],
        // ],
        // 'production' => [
        //     'array' => [
        //         'configuration' => [
        //           'logging.default' => 'stack'
        //         ],
        //     ],
        //     'aws',
        // ],
    ],
];
