<?php

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/TestHelper.php';

require_once 'Atomik.php';

class AtomikTest extends PHPUnit_Framework_TestCase
{
	public function testUriMatch()
	{
		$uri = 'index';
		$this->assertTrue(Atomik::uriMatch('/index/', $uri));
		$this->assertTrue(Atomik::uriMatch('/index/*', $uri));
		$uri = 'dir/index';
		$this->assertTrue(Atomik::uriMatch('/dir/*', $uri));
		$this->assertFalse(Atomik::uriMatch('/dir', $uri));
	}
	
	public function testRoute()
	{
		
	}
	
	public function testExecuteInScope()
	{
		
	}
	
	public function testRenderFile()
	{
		
	}
	
	public function testDisableLayout()
	{
		
	}
	
	public function testMergeRecursive()
	{
		$array1 = array('key1' => array('key1.1' => array('value1.1-1')));
		$array2 = array('key1' => array('key1.1' => array('value1.1-2')));
		$array = Atomik::_mergeRecursive($array1, $array2);
		$this->assertContains('value1.1-2', $array['key1']['key1.1']);
	}
	
	public function testDimensionizeArraySimple()
	{
		$array = array('key1/key1.1' => 'value1.1-1');
		$dimensionizedArray = Atomik::_dimensionizeArray($array);
		$this->assertArrayHasKey('key1', $dimensionizedArray);
		$this->assertArrayHasKey('key1.1', $dimensionizedArray['key1']);
		$this->assertEquals('value1.1-1', $dimensionizedArray['key1']['key1.1']);
	}
	
	public function testDimensionizeArrayComplex()
	{
		$array = array(
			'key1/key1.1' => array(
				'key1.1.1' => 'value1.1.1-1'
			),
			'key1/key1.1/key1.1.2' => 'value1.1.2-1',
			'key2/key2.1' => array(
				'key2.1.1/key2.1.1.1' => 'value2.1.1.1-1'
			)
		);
		
		$dimensionizedArray = Atomik::_dimensionizeArray($array);
		
		$this->assertArrayHasKey('key1', $dimensionizedArray);
		$this->assertArrayHasKey('key1.1', $dimensionizedArray['key1']);
		$this->assertArrayHasKey('key1.1.1', $dimensionizedArray['key1']['key1.1']);
		$this->assertArrayHasKey('key1.1.2', $dimensionizedArray['key1']['key1.1']);
		$this->assertEquals('value1.1.1-1', $dimensionizedArray['key1']['key1.1']['key1.1.1']);
		$this->assertEquals('value1.1.2-1', $dimensionizedArray['key1']['key1.1']['key1.1.2']);
		
		$this->assertArrayHasKey('key2', $dimensionizedArray);
		$this->assertArrayHasKey('key2.1', $dimensionizedArray['key2']);
		$this->assertArrayHasKey('key2.1.1', $dimensionizedArray['key2']['key2.1']);
		$this->assertArrayHasKey('key2.1.1.1', $dimensionizedArray['key2']['key2.1']['key2.1.1']);
		$this->assertEquals('value2.1.1.1-1', $dimensionizedArray['key2']['key2.1']['key2.1.1']['key2.1.1.1']);
	}
	
	public function testSetWithSimpleKey()
	{
		$array = array();
		Atomik::set('foo', 'bar', true, $array);
		$this->assertArrayHasKey('foo', $array);
		$this->assertEquals('bar', $array['foo']);
	}
	
	public function testSetWithPathKey()
	{
		$array = array();
		Atomik::set('foo/bar', 'oof', true, $array);
		$this->assertArrayHasKey('foo', $array);
		$this->assertArrayHasKey('bar', $array['foo']);
		$this->assertEquals('oof', $array['foo']['bar']);
	}
	
	public function testSetWithArray()
	{
		$array = array();
		Atomik::set(array('foo' => 'bar'), null, true, $array);
		$this->assertArrayHasKey('foo', $array);
		$this->assertEquals('bar', $array['foo']);
	}
	
	public function testSetWithoutDimensionize()
	{
		$array = array();
		Atomik::set(array('foo/bar' => 'oof'), null, false, $array);
		$this->assertArrayHasKey('foo/bar', $array);
		$this->assertEquals('oof', $array['foo/bar']);
	}
	
	public function testAddInArray()
	{
		$array = array('key' => 'value');
		Atomik::add('foo', 'bar', true, $array);
		$this->assertArrayHasKey('key', $array);
		$this->assertArrayHasKey('foo', $array);
		$this->assertEquals('value', $array['key']);
		$this->assertEquals('bar', $array['foo']);
	}
	
	public function testAddWithStringValue()
	{
		$array = array('key' => 'value');
		Atomik::add('key', 'foo', true, $array);
		$this->assertArrayHasKey('key', $array);
		$this->assertTrue(is_array($array['key']));
		$this->assertContains('value', $array['key']);
		$this->assertContains('foo', $array['key']);
	}
	
	public function testGetWithKey()
	{
		$array = array('key' => 'value');
		$this->assertEquals('value', Atomik::get('key', null, $array));
	}
	
	public function testGetWithDefaultValue()
	{
		$array = array();
		$this->assertEquals('default', Atomik::get('key', 'default', $array));
	}
	
	public function testGetWithPath()
	{
		$array = array(
			'sub' => array(
				'key' => 'value'
			)
		);
		
		$this->assertEquals('value', Atomik::get('sub/key', null, $array));
	}
	
	public function testHas()
	{
		
	}
	
	public function testDelete()
	{
		
	}
	
	public function testReset()
	{
		
	}
	
	public function testSelector()
	{
		
	}
	
	public function testCall()
	{
		
	}
	
	public function testEvents()
	{
		
	}
	
	public function testPath()
	{
		
	}
	
	public function testUrl()
	{
		
	}
	
	public function testPluginAsset()
	{
		
	}
	
	public function testNeeded()
	{
		
	}
	
	public function testEscape()
	{
		
	}
	
	public function testFlash()
	{
		
	}
	
	public function testFilter()
	{
		
	}
	
	public function testFriendlify()
	{
		
	}
}