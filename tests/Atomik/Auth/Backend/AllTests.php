<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Auth_Backend_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Atomik/Auth/Backend/DbTest.php';
require_once 'Atomik/Auth/Backend/FactoryTest.php';

class Atomik_Auth_Backend_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Auth_Backend');

        $suite->addTestSuite('Atomik_Auth_Backend_DbTest');
        $suite->addTestSuite('Atomik_Auth_Backend_FactoryTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Auth_Backend_AllTests::main') {
    Atomik_Auth_Backend_AllTests::main();
}
