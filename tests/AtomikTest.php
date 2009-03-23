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
		$routes = array(
			'/index' => array('action' => 'notindex'),
			'/dir/action' => array('action' => 'dir'),
			'/get/:id' => array('action' => 'get'),
			'/opt/:id' => array('action' => 'opt', 'id' => 0),
			'/multi/:p1/:p2' => array('action' => 'multi'),
			'/ext/:action.:ext' => array()
		);
		
		$request = Atomik::route('/home', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertEquals('home', $request['action']);
		
		$request = Atomik::route('/sub/home', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertEquals('sub/home', $request['action']);
		
		$request = Atomik::route('/index', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertEquals('notindex', $request['action']);
		
		$request = Atomik::route('/dir/action', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertEquals('dir', $request['action']);
		
		$request = Atomik::route('/get', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertArrayNotHasKey('id', $request);
		
		$request = Atomik::route('/get/1', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertArrayHasKey('id', $request);
		$this->assertEquals('get', $request['action']);
		$this->assertEquals(1, $request['id']);
		
		$request = Atomik::route('/opt', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertArrayHasKey('id', $request);
		$this->assertEquals('opt', $request['action']);
		$this->assertEquals(0, $request['id']);
		
		$request = Atomik::route('/opt/1', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertArrayHasKey('id', $request);
		$this->assertEquals('opt', $request['action']);
		$this->assertEquals(1, $request['id']);
		
		$request = Atomik::route('/multi/1/2', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertArrayHasKey('p1', $request);
		$this->assertArrayHasKey('p2', $request);
		$this->assertEquals('multi', $request['action']);
		$this->assertEquals(1, $request['p1']);
		$this->assertEquals(2, $request['p2']);
		
		$request = Atomik::route('/ext/index.xml', array(), $routes);
		$this->assertArrayHasKey('action', $request);
		$this->assertArrayHasKey('ext', $request);
		$this->assertEquals('index', $request['action']);
		$this->assertEquals('xml', $request['ext']);
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
	
	public function testHasWithKey()
	{
		$array = array('key' => 'value');
		$this->assertTrue(Atomik::has('key', $array));
		$this->assertFalse(Atomik::has('foo', $array));
	}
	
	public function testHasWithPath()
	{
		$array = array('sub' => array('key' => 'value'));
		$this->assertTrue(Atomik::has('sub/key', $array));
		$this->assertFalse(Atomik::has('sub/foo', $array));
	}
	
	public function testDeleteWithKey()
	{
		$array = array('key' => 'value');
		Atomik::delete('key', $array);
		$this->assertNotContains('key', $array);
	}
	
	public function testDeleteWithPath()
	{
		$array = array('sub' => array('key' => 'value'));
		Atomik::delete('sub/key', $array);
		$this->assertArrayHasKey('sub', $array);
		$this->assertArrayNotHasKey('key', $array['sub']);
	}
	
	public function testReset()
	{
		Atomik::reset('key', 'default');
		$this->assertEquals('default', Atomik::get('key'));
		Atomik::set('key', 'value');
		$this->assertEquals('value', Atomik::get('key'));
		Atomik::reset();
		$this->assertEquals('default', Atomik::get('key'));
	}
	
	public function testDisableLayout()
	{
		Atomik::disableLayout();
		$this->assertTrue(Atomik::get('app/disable_layout'));
	}
	
	public function myTestCallback($string)
	{
		return strtoupper($string);
	}
	
	public function testSelector()
	{
		Atomik::registerSelector('up', array($this, 'myTestCallback'));
		$this->assertEquals('HELLO WORLD', Atomik::get('up:hello world'));
	}
	
	public function testCall()
	{
		Atomik::registerMethod('up', array($this, 'myTestCallback'));
		$this->assertEquals('HELLO WORLD', Atomik::call('up', 'hello world'));
	}
	
	public function myTestEventCallback($string)
	{
		echo $string;
	}
	
	public function testEvents()
	{
        Atomik::listenEvent('print', array($this, 'myTestEventCallback'));
        
        ob_start();
        Atomik::fireEvent('print', array('foo'));
        $output = ob_get_clean();
        
        $this->assertEquals('foo', $output);
	}
	
	public function testPath()
	{
		$setOfPaths = array('/path1', '/path2');
		$file = 'file.txt';
		
		$this->assertEquals('/path1', Atomik::path($setOfPaths, false, false));
		$this->assertEquals($setOfPaths, Atomik::path($setOfPaths, true, false));
		$this->assertType('array', Atomik::path('/path1', true, false));
		
		$this->assertEquals('/path1/file.txt', Atomik::path($file, $setOfPaths, false));
	}
	
	public function testUrlWithIndexPhp()
	{
		Atomik::set('atomik/base_url', '');
		$this->assertEquals('/index.php?action=index', Atomik::url('/index'));
		Atomik::set('atomik/trigger', 'uri');
		$this->assertEquals('/index.php?uri=index', Atomik::url('/index'));
	}
	
	public function testUrlWithParams()
	{
		Atomik::set('atomik/base_url', '');
		$this->assertEquals('/show/1', Atomik::url('/show/:id', array('id' => 1), false));
		$this->assertEquals('/show/1?order=asc', Atomik::url('/show/:id', array('id' => 1, 'order' => 'asc'), false));
	}
	
	public function testAsset()
	{
		$this->assertEquals('/style.css', Atomik::asset('style.css'));
	}
	
	public function testPluginAsset()
	{
		$this->assertEquals('/app/plugins/Plugin/assets/style.css', Atomik::pluginAsset('style.css', 'plugin'));
		Atomik::set('atomik/plugin_assets_tpl', 'plugins/%s/assets');
		$this->assertEquals('/plugins/Plugin/assets/style.css', Atomik::pluginAsset('style.css', 'plugin'));
	}
	
	public function testPluginUrl()
	{
		Atomik::set('atomik/base_action', 'plugin');
		$this->assertEquals('/plugin/index', Atomik::pluginUrl('index', array(), false));
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