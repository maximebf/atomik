<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Builder_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Atomik/Builder/PluginTest.php';

class Atomik_Builder_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Builder');

        $suite->addTestSuite('Atomik_Builder_PluginTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Builder_AllTests::main') {
    Atomik_Builder_AllTests::main();
}
