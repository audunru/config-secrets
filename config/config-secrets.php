<?php

use audunru\ConfigSecrets\ConfigProviders\ArrayConfigProvider;
use audunru\ConfigSecrets\ConfigProviders\AwsConfigProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Console options
    |--------------------------------------------------------------------------
    |
    | When running console commands, determine if the configuration should be
    | updated.
    */

    'console' => [
        /*
        |--------------------------------------------------------------------------
        | Include console commands
        |--------------------------------------------------------------------------
        |
        | Update configuration only when these console commands are run. If empty,
        | will update on all commands.
        |
        */

        'include' => [],

        /*
        |--------------------------------------------------------------------------
        | Exclude console commands
        |--------------------------------------------------------------------------
        |
        | Do not update configuration when these console commands are run.
        |
        */

        'exclude' => [],
    ],

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

            'provider' => ArrayConfigProvider::class,

            /*
            |--------------------------------------------------------------------------
            | Log
            |--------------------------------------------------------------------------
            |
            | Set to true to log how many configuration values this provider supplied.
            |
            */

            'log' => false,

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
        'aws' => [
            'provider' => AwsConfigProvider::class,

            /*
            |--------------------------------------------------------------------------
            | Log
            |--------------------------------------------------------------------------
            |
            | Set to true to log how many configuration values this provider supplied.
            |
            */

            'log' => false,

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
            | Only secrets whose name starts with this value will be retrieved. AWS matches
            | the name as a prefix, so "myapp" also matches "myapp-staging". Leave empty to
            | retrieve all secrets in the AWS account.
            |
            */

            'secret-name' => env('AWS_SECRET_NAME', ''),

            /*
            |--------------------------------------------------------------------------
            | Tag used to return list of secrets
            |--------------------------------------------------------------------------
            |
            | Only secrets with a tag key that starts with "tag-key" and a tag value that
            | starts with "tag-value" will be retrieved. AWS matches both as prefixes, and as
            | two separate filters, so they do not have to match the same tag. Both must be
            | set for the filter to apply. Leave empty to retrieve all secrets in the AWS account.
            |
            */

            'tag-key' => env('AWS_SECRETS_TAG_KEY', ''),
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
