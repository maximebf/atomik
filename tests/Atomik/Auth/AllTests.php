<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Auth_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Atomik/Auth/Backend/AllTests.php';
require_once 'Atomik/Auth/User/AllTests.php';
require_once 'Atomik/Auth/UserTest.php';

class Atomik_Auth_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Auth');

        $suite->addTest(Atomik_Auth_Backend_AllTests::suite());
        $suite->addTestSuite('Atomik_Auth_UserTest');
        $suite->addTest(Atomik_Auth_User_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Auth_AllTests::main') {
    Atomik_Auth_AllTests::main();
}
