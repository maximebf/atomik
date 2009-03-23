<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Db_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Atomik/Db/InstanceTest.php';
require_once 'Atomik/Db/QueryTest.php';

class Atomik_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Db');

        $suite->addTestSuite('Atomik_Db_InstanceTest');
        $suite->addTestSuite('Atomik_Db_QueryTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Db_AllTests::main') {
    Atomik_Db_AllTests::main();
}
