<?php

namespace Atomik\Tests;

use Atomik;

class AtomikTest extends AtomikTestCase
{
    public function testUriMatch()
    {
        $uri = 'index';
        $this->assertTrue(Atomik::uriMatch('/index/', $uri));
        $this->assertTrue(Atomik::uriMatch('/index*', $uri));
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
        $array1 = array('key1' => array('key11' => array('value111')));
        $array2 = array('key1' => array('key11' => array('value112')));
        $array = Atomik::mergeRecursive($array1, $array2);
        $this->assertContains('value112', $array['key1']['key11']);
    }
    
    public function testDimensionizeArraySimple()
    {
        $array = array('key1.key11' => 'value111');
        $dimensionizedArray = Atomik::dimensionizeArray($array);
        $this->assertArrayHasKey('key1', $dimensionizedArray);
        $this->assertArrayHasKey('key11', $dimensionizedArray['key1']);
        $this->assertEquals('value111', $dimensionizedArray['key1']['key11']);
    }
    
    public function testDimensionizeArrayComplex()
    {
        $array = array(
            'key1.key11' => array(
                'key111' => 'value1111'
            ),
            'key1.key11.key112' => 'value1121',
            'key2.key21' => array(
                'key211.key2111' => 'value21111'
            )
        );
        
        $dimensionizedArray = Atomik::dimensionizeArray($array);
        
        $this->assertArrayHasKey('key1', $dimensionizedArray);
        $this->assertArrayHasKey('key11', $dimensionizedArray['key1']);
        $this->assertArrayHasKey('key111', $dimensionizedArray['key1']['key11']);
        $this->assertArrayHasKey('key112', $dimensionizedArray['key1']['key11']);
        $this->assertEquals('value1111', $dimensionizedArray['key1']['key11']['key111']);
        $this->assertEquals('value1121', $dimensionizedArray['key1']['key11']['key112']);
        
        $this->assertArrayHasKey('key2', $dimensionizedArray);
        $this->assertArrayHasKey('key21', $dimensionizedArray['key2']);
        $this->assertArrayHasKey('key211', $dimensionizedArray['key2']['key21']);
        $this->assertArrayHasKey('key2111', $dimensionizedArray['key2']['key21']['key211']);
        $this->assertEquals('value21111', $dimensionizedArray['key2']['key21']['key211']['key2111']);
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
        Atomik::set('foo.bar', 'oof', true, $array);
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
        Atomik::set(array('foo.bar' => 'oof'), null, false, $array);
        $this->assertArrayHasKey('foo.bar', $array);
        $this->assertEquals('oof', $array['foo.bar']);
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
        
        $this->assertEquals('value', Atomik::get('sub.key', null, $array));
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
        $this->assertTrue(Atomik::has('sub.key', $array));
        $this->assertFalse(Atomik::has('sub.foo', $array));
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
        Atomik::delete('sub.key', $array);
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
        $this->assertTrue(Atomik::get('app.disable_layout'));
    }
    
    public function myTestCallback($string)
    {
        return strtoupper($string);
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
    
    public function testUrlWithIndexPhp()
    {
        Atomik::set('atomik.base_url', '');
        $this->assertEquals('/index.php?action=index', Atomik::url('/index'));
        Atomik::set('atomik.trigger', 'uri');
        $this->assertEquals('/index.php?uri=index', Atomik::url('/index'));
    }
    
    public function testUrlWithParams()
    {
        Atomik::set('atomik.base_url', '');
        $this->assertEquals('/show/1', Atomik::url('/show/:id', array('id' => 1), false));
        $this->assertEquals('/show/1?order=asc', Atomik::url('/show/:id', array('id' => 1, 'order' => 'asc'), false));
    }
    
    public function testAsset()
    {
        $this->assertEquals('/style.css', Atomik::asset('style.css'));
    }
    
    public function testPluginAsset()
    {
        $this->assertEquals('/app/plugins/Plugin/assets/style.css', Atomik::pluginAsset('Plugin', 'style.css'));
        Atomik::set('atomik.plugin_assets_tpl', 'plugins/%s/assets');
        $this->assertEquals('/plugins/Plugin/assets/style.css', Atomik::pluginAsset('Plugin', 'style.css'));
    }
    
    public function testPluginUrl()
    {
        Atomik::set('atomik.base_action', 'plugin');
        Atomik::registerPluggableApplication('Test');
        $this->assertEquals('/test/hello', Atomik::pluginUrl('Test', 'hello', array(), false));
    }
}
