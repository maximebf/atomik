<?php
/*
 * Inspired by Zend Framework TestHelper.php
 * http://framework.zend.com 
 */

/*
 * Start output buffering
 */
ob_start();

/*
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

/*
 * Set error reporting to the level to which Atomik Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

$rootDir = dirname(__FILE__) . '/..';

/*
 * Omit from code coverage reports the contents of the tests directory
 */
foreach (array('php', 'phtml', 'csv') as $suffix) {
    PHPUnit_Util_Filter::addDirectoryToFilter($rootDir . '/tests', ".$suffix");
}

$path = array(
    $rootDir,
    $rootDir . '/library',
    $rootDir . '/tests',
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $path));

/*
 * Avoid Atomik from automatically starting
 */
define('ATOMIK_AUTORUN', false);

/*
 * Add Atomik Framework library/ directory to the PHPUnit code coverage
 * whitelist. This has the effect that only production code source files appear
 * in the code coverage report and that all production code source files, even
 * those that are not covered by a test yet, are processed.
 */
if (version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($rootDir . '/library');
}

/*
 * Unset global variables that are no longer needed.
 */
unset($rootDir, $path);
