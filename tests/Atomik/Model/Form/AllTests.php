<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Model_Form_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

class Atomik_Model_Form_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Model_Form');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Model_Form_AllTests::main') {
    Atomik_Model_Form_AllTests::main();
}
