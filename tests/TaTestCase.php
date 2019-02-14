<?php

use SsnTestKit\WpTestCase;

class TaTestCase extends WpTestCase
{
    protected function browserBaseUri()
    {
        return 'http://local.wordpress.test';
    }

    protected function wpSqlDump()
    {
        return __DIR__ . '/fixtures/vvv-base-plus-ta-plugin-and-theme.sql';
    }
}
