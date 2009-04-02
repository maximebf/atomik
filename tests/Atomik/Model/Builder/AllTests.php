<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Atomik_Model_Builder_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Atomik/Model/Builder/CacheTest.php';
require_once 'Atomik/Model/Builder/ClassMetadataTest.php';
require_once 'Atomik/Model/Builder/FactoryTest.php';
require_once 'Atomik/Model/Builder/FieldTest.php';
require_once 'Atomik/Model/Builder/ReferenceTest.php';

class Atomik_Model_Builder_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Atomik Framework - Atomik_Model_Builder');

        $suite->addTestSuite('Atomik_Model_Builder_CacheTest');
        $suite->addTestSuite('Atomik_Model_Builder_ClassMetadataTest');
        $suite->addTestSuite('Atomik_Model_Builder_FactoryTest');
        $suite->addTestSuite('Atomik_Model_Builder_FieldTest');
        $suite->addTestSuite('Atomik_Model_Builder_ReferenceTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Atomik_Model_Builder_AllTests::main') {
    Atomik_Model_Builder_AllTests::main();
}
