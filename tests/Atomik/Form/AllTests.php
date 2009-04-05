<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Form_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Atomik/Form/ClassTest.php';
require_once 'Atomik/Form/FieldsetTest.php';
require_once 'Atomik/Form/Field/AllTests.php';

class Atomik_Form_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Form');

        $suite->addTestSuite('Atomik_Form_ClassTest');
        $suite->addTestSuite('Atomik_Form_FieldsetTest');
        $suite->addTest(Atomik_Form_Field_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Form_AllTests::main') {
    Atomik_Form_AllTests::main();
}
