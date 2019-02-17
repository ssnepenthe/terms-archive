<?php

use SsnTestKit\ResetsSite;
use SsnTestKit\StkTestCase;
use SsnTestKit\ManagesThemes;
use SsnTestKit\ManagesPlugins;

class TaTestCase extends StkTestCase
{
    use ManagesPlugins,
        ManagesThemes,
        ResetsSite;

    protected function browserBaseUri()
    {
        return 'http://local.wordpress.test';
    }

    protected function wpSqlDump()
    {
        return __DIR__ . '/fixtures/vvv-base-install.sql';
    }
}
