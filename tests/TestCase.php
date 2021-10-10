<?php

namespace audunru\ConfigSecrets\Tests;

use audunru\ConfigSecrets\ConfigSecretsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @SuppressWarnings("unused")
     */
    protected function getPackageProviders($app)
    {
        return [ConfigSecretsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->register(ConfigSecretsServiceProvider::class);
    }
}
