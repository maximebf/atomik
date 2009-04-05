<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Form_Field_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';


class Atomik_Form_Field_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Form_Field');

        $suite->addTestSuite('Atomik_Form_Field_FactoryTest');
        $suite->addTestSuite('Atomik_Form_Field_DateTest');
        $suite->addTestSuite('Atomik_Form_Field_FileTest');
        $suite->addTestSuite('Atomik_Form_Field_InputTest');
        $suite->addTestSuite('Atomik_Form_Field_TextareaTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Form_Field_AllTests::main') {
    Atomik_Form_Field_AllTests::main();
}
