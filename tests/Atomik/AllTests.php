<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

require_once 'Atomik/AuthTest.php';
require_once 'Atomik/Builder/AllTests.php';
require_once 'Atomik/BuilderTest.php';
require_once 'Atomik/ConfigTest.php';
require_once 'Atomik/DbTest.php';
require_once 'Atomik/ManifestTest.php';
require_once 'Atomik/ModelTest.php';

class Atomik_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik');

        $suite->addTestSuite('Atomik_AuthTest');
        $suite->addTest(Atomik_Builder_AllTests::suite());
        $suite->addTestSuite('Atomik_BuilderTest');
        $suite->addTestSuite('Atomik_ConfigTest');
        $suite->addTestSuite('Atomik_DbTest');
        $suite->addTestSuite('Atomik_ManifestTest');
        $suite->addTestSuite('Atomik_ModelTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_AllTests::main') {
    Atomik_AllTests::main();
}
