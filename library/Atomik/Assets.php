<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2009 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Atomik
 * @subpackage Assets
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * @package Atomik
 * @subpackage Assets
 */
class Atomik_Assets
{
	const CSS = 'text/css';
	const JS = 'text/javascript';
	
	/**
	 * @var string
	 */
	protected $_baseUrl;
	
	/**
	 * @var callback
	 */
	protected $_urlFormater;
	
	/**
	 * @var array
	 */
	protected $_namedAssets = array();
	
	/**
	 * @var array
	 */
	protected $_assets = array();
	
	/**
	 * @var Atomik_Assets
	 */
	private static $_instance;
	
	/**
	 * @var string
	 */
	private static $_defaultBaseUrl = '';
	
	/**
	 * @var callback
	 */
	private static $_defaultUrlFormater;
	
	/**
	 * @param string $baseUrl
	 */
	public static function setDefaultBaseUrl($baseUrl)
	{
	    self::$_defaultBaseUrl = $baseUrl;
	}
	
	/**
	 * @return string
	 */
	public static function getDefaultBaseUrl()
	{
	    return self::$_defaultBaseUrl;
	}
	
	/**
	 * @param callback $callback
	 */
	public static function setDefaultUrlFormater($callback)
	{
	    self::$_defaultUrlFormater = $callback;
	}
	
	/**
	 * @return callback
	 */
	public static function getDefaultUrlFormater()
	{
	    return self::$_defaultUrlFormater;
	}
	
	/**
	 * @return Atomik_Assets
	 */
	public static function getInstance()
	{
	    if (self::$_instance === null) {
	        self::$_instance = new Atomik_Assets();
	    }
	    return self::$_instance;
	}
	
	public function __construct()
	{
	    $this->_baseUrl = self::$_defaultBaseUrl;
	    $this->_urlFormater = self::$_defaultUrlFormater;
	}
	
	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->_baseUrl = $baseUrl;
	}
	
	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->_baseUrl;
	}
	
	/**
	 * Sets a callback to use to format the url when rendered
	 * 
	 * @param callback $callback
	 */
	public function setUrlFormater($callback)
	{
		$this->_urlCallback = $callback;
	}
	
	/**
	 * Returns the callback used to format the urls
	 * 
	 * @return callback
	 */
	public function getUrlFormater()
	{
		return $this->_urlCallback;
	}
	
	/**
	 * Resets registered named assets
	 * 
	 * @param array $assets
	 */
	public function registerNamedAssets($assets)
	{
		foreach ($assets as $name => $asset) {
			if (is_int($name)) {
				$name = $asset['name'];
			}
			$this->registerNamedAsset($name, $asset);
		}
	}
	
	/**
	 * Registers a named asset
	 * 
	 * @param	string			$name
	 * @param	string|array	$url
	 * @param 	string			$type
	 * @param	array			$dependencies
	 */
	public function registerNamedAsset($name, $url, $type = null, $dependencies = array())
	{
		if (is_array($url)) {
		    $assets = array();
			foreach ($url as $asset) {
			    if (is_array($asset)) {
			        $asset['name'] = $name;
			        $assets[] = $asset;
			    } else {
			        $assets[] = $this->createAsset($asset);
			    }
			}
			$assets['name'] = $name;
			$this->_namedAssets[$name] = $assets;
		} else {
			$this->_namedAssets[$name] = $this->createAsset($url, $type, $dependencies, $name);
		}
	}
	
	/**
	 * Checks if a named asset is registered
	 * 
	 * @param 	string	$name
	 * @return 	bool
	 */
	public function isNamedAssetRegistered($name)
	{
		return isset($this->_namedAssets[$name]);
	}
	
	/**
	 * Returns all registered assets
	 * 
	 * @return array
	 */
	public function getRegisteredNamedAssets()
	{
		return $this->_namedAssets;
	}
	
	/**
	 * Creates an asset array
	 * 
	 * @param	string	$url
	 * @param 	string	$type
	 * @param	array	$dependencies
	 * @param 	string	$name
	 * @return 	array
	 */
	public function createAsset($url, $type = null, $dependencies = array(), $name = null)
	{
		return array(
			'name'	=> $name,
			'url'	=> $url,
			'type'	=> $this->_getAssetType($url, $type),
			'dependencies' => $dependencies
		);
	}
	
	/**
	 * Adds a named asset
	 * 
	 * @param $name
	 * @param $allowTwice
	 */
	public function addNamedAsset($name)
	{
		if (!$this->isNamedAssetRegistered($name)) {
			return false;
		}
		
		if ($this->hasNamedAsset($name)) {
			return true;
		}
		
		$asset = $this->_namedAssets[$name];
		if (!isset($asset['url'])) {
			foreach ($asset as $a) {
				if (is_array($a)) {
					$this->_addAssetWithDependencies($a, $a['dependencies']);
				}
			}
		} else {
			$this->_addAssetWithDependencies($asset, $asset['dependencies']);
		}
		
		return true;
	}
	
	/**
	 * Adds multiple assets at a time
	 * 
	 * @param array $assets
	 */
	public function addAssets($assets)
	{
	    foreach ($assets as $asset) {
	        if (is_array($asset)) {
	            $this->addAsset($asset['url'], $asset['type'], $asset['dependencies']);
	        } else {
	            $this->addAsset($asset);
	        }
	    }
	}
	
	/**
	 * Adds a new asset file
	 * 
	 * @param	string	$url
	 * @param	string	$type
	 * @param	array	$dependencies
	 * @param 	bool	$allowTwice		Whether the asset can be added twice
	 */
	public function addAsset($url, $type = null, $dependencies = array(), $allowTwice = false)
	{
		if (!$allowTwice && $this->hasAsset($url, $type)) {
			return false;
		}
		
		$this->_addAssetWithDependencies($this->createAsset($url, $type), $dependencies);
		return true;
	}
	
	/**
	 * Adds an asset and all its dependencies
	 * 
	 * @param	array	$asset
	 * @param 	array	$dependencies
	 */
	private function _addAssetWithDependencies($asset, $dependencies = array())
	{
		if (!empty($dependencies)) {
			foreach ($dependencies as $dependency) {
				if (!$this->addNamedAsset($dependency)) {
					require_once 'Atomik/Assets/Exception.php';
					throw new Atomik_Assets_Exception('Asset dependency not found: ' . $dependency);
				}
			}
		}
		
		$this->_assets[] = $asset;
	}
	
	/**
	 * Checks if an asset exists
	 * 
	 * @param	string	$url
	 * @param	string	$type
	 * @return	bool
	 */
	public function hasAsset($url, $type = null)
	{
		foreach ($this->_assets as $asset) {
			if ($asset['url'] == $url && ($type === null || $asset['type'] == $type)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks if a named asset exists
	 * 
	 * @param	string	$name
	 * @return	bool
	 */
	public function hasNamedAsset($name)
	{
		foreach ($this->_assets as $asset) {
			if (isset($asset['name']) && $asset['name'] == $name) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Removes an asset
	 * 
	 * @param	string	$url
	 * @param	string	$type
	 */
	public function removeAsset($url, $type = null)
	{
		for ($i = 0, $c = count($this->_assets); $i < $c; $i++) {
			if ($this->_assets[$i]['url'] == $url && ($type === null || $this->_assets[$i]['type'] == $type)) {
				unset($this->_assets[$i]);
				return;
			}
		}
	}
	
	/**
	 * Removes an asset
	 * 
	 * @param	string	$url
	 * @param	string	$type
	 */
	public function removeNamedAsset($name)
	{
		for ($i = 0, $c = count($this->_assets); $i < $c; $i++) {
			if ($this->_assets[$i]['name'] == $name) {
				unset($this->_assets[$i]);
			}
		}
	}
	
	/**
	 * Returns a list of assets according to the filter
	 * 
	 * @param	string	$type	Null for all
	 * @return 	array
	 */
	public function getAssets($type = null)
	{
		$assets = array();
		foreach ($this->_assets as $asset) {
			if ($type === null || $asset['type'] == $type) {
				$assets[] = $asset['url'];
			}
		}
		return $assets;
	}
	
	/**
	 * Adds a new asset of type text/css
	 * 
	 * @see Atomik_Assets::addAsset()
	 * @param 	string	$url
	 * @param 	array	$dependencies
	 */
	public function addStyle($url, $dependencies = array())
	{
		$this->addAsset($url, self::CSS, $dependencies);
	}
	
	/**
	 * Returns all assets filenames of type text/css
	 * 
	 * @return 	array
	 */
	public function getStyles()
	{
		return $this->getAssets(self::CSS);
	}
	
	/**
	 * Renders the link tag for styles
	 * 
	 * @return 	string
	 */
	public function renderStyles()
	{
		$html = '';
		foreach ($this->getStyles() as $url) {
			$html .= sprintf('<link rel="stylesheet" type="text/css" href="%s" />' 
			       . "\n", $this->_formatUrl($url, $this->_baseUrl));
		}
		return $html;
	}
	
	/**
	 * Adds a new asset of type text/javascript
	 * 
	 * @see Atomik_Assets::addAsset()
	 * @param 	string	$url
	 * @param 	array	$dependencies
	 */
	public function addScript($url, $dependencies = array())
	{
		$this->addAsset($url, self::JS, $dependencies);
	}
	
	/**
	 * Returns all assets filenames of type text/javascript
	 * 
	 * @return 	array
	 */
	public function getScripts()
	{
		return $this->getAssets(self::JS);
	}
	
	/**
	 * Renders the script tag for scripts
	 * 
	 * @return 	string
	 */
	public function renderScripts()
	{
		$html = '';
		foreach ($this->getScripts() as $url) {
			$html .= sprintf('<script type="text/javascript" src="%s"></script>' 
			       . "\n", $this->_formatUrl($url, $this->_baseUrl));
		}
		return $html;
	}
	
	/**
	 * Renders all assets
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->renderStyles() . $this->renderScripts();
	}
	
	/**
	 * Returns an asset type depending on its filename
	 * 
	 * @param	string	$url
	 * @param	string	$userType
	 * @return	string
	 */
	private function _getAssetType($url, $userType = null)
	{
		if ($userType !== null) {
			return $userType;
		}
		
		if (strpos($url, '.') === false) {
			return self::CSS;
		}
		
		$extension = strtolower(substr($url, strrpos($url, '.') + 1));
		switch ($extension) {
			case 'css':
				return self::CSS;
			case 'js':
				return self::JS;
		}
		
		return self::CSS;
	}
	
	/**
	 * Formats a url using the specified url callback
	 * 
	 * @param	string	$url
	 * @return	string
	 */
	private function _formatUrl($url, $baseUrl = '')
	{
	    $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
		if ($this->_urlFormater === null) {
			return $url;
		}
		return call_user_func($this->_urlFormater, $url);
	}
}