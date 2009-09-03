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
	 * @var callback
	 */
	private static $_urlCallback;
	
	/**
	 * @var array
	 */
	private static $_namedAssets = array();
	
	/**
	 * @var array
	 */
	private static $_assets = array();
	
	/**
	 * Sets a callback to use to format the url when rendered
	 * 
	 * @param callback $callback
	 */
	public static function setUrlFormater($callback)
	{
		self::$_urlCallback = $callback;
	}
	
	/**
	 * Returns the callback used to format the urls
	 * 
	 * @return callback
	 */
	public static function getUrlFormater()
	{
		return self::$_urlCallback;
	}
	
	/**
	 * Resets registered named assets
	 * 
	 * @param array $assets
	 */
	public static function setRegisteredNamedAssets($assets)
	{
		self::$_namedAssets = array();
		foreach ($assets as $name => $asset) {
			if (is_int($name)) {
				$name = $asset['name'];
			} else {
				$asset['name'] = $name;
			}
			self::$_namedAssets[$name] = $asset;
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
	public static function registerNamedAsset($name, $url, $type = null, $dependencies = array())
	{
		if (is_array($url)) {
			foreach ($url as &$asset) {
				$asset['name'] = $name;
			}
			$url['name'] = $name;
			self::$_namedAssets[$name] = $url;
		} else {
			self::$_namedAssets[$name] = self::createAsset($url, $type, $dependencies, $name);
		}
	}
	
	/**
	 * Checks if a named asset is registered
	 * 
	 * @param 	string	$name
	 * @return 	bool
	 */
	public static function isNamedAssetRegistered($name)
	{
		return isset(self::$_namedAssets[$name]);
	}
	
	/**
	 * Returns all registered assets
	 * 
	 * @return array
	 */
	public static function getRegisteredNamedAssets()
	{
		return self::$_namedAssets;
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
	public static function createAsset($url, $type = null, $dependencies = array(), $name = null)
	{
		return array(
			'name'	=> $name,
			'url'	=> $url,
			'type'	=> self::_getAssetType($url, $type),
			'dependencies' => $dependencies
		);
	}
	
	/**
	 * Adds a named asset
	 * 
	 * @param $name
	 * @param $allowTwice
	 */
	public static function addNamedAsset($name)
	{
		if (!self::isNamedAssetRegistered($name)) {
			return false;
		}
		
		if (self::hasNamedAsset($name)) {
			return true;
		}
		
		$asset = self::$_namedAssets[$name];
		if (!isset($asset['url'])) {
			foreach ($asset as $a) {
				if (is_array($a)) {
					self::_addAssetWithDependencies($a, $a['dependencies']);
				}
			}
		} else {
			self::_addAssetWithDependencies($asset, $asset['dependencies']);
		}
		
		return true;
	}
	
	/**
	 * Adds a new asset file
	 * 
	 * @see Atomik::pluginAsset()
	 * @param	string	$url
	 * @param	string	$type
	 * @param	array	$dependencies
	 * @param 	bool	$allowTwice		Whether the asset can be added twice
	 */
	public static function addAsset($url, $type = null, $dependencies = array(), $allowTwice = false)
	{
		if (!$allowTwice && self::hasAsset($url, $type)) {
			return false;
		}
		
		self::_addAssetWithDependencies(self::createAsset($url, $type), $dependencies);
		return true;
	}
	
	/**
	 * Adds an asset and all its dependencies
	 * 
	 * @param	array	$asset
	 * @param 	array	$dependencies
	 */
	private static function _addAssetWithDependencies($asset, $dependencies = array())
	{
		if (!empty($dependencies)) {
			foreach ($dependencies as $dependency) {
				if (!self::addNamedAsset($dependency)) {
					require_once 'Atomik/Assets/Exception.php';
					throw new Atomik_Assets_Exception('Asset dependency not found: ' . $dependency);
				}
			}
		}
		
		self::$_assets[] = $asset;
	}
	
	/**
	 * Checks if an asset exists
	 * 
	 * @param	string	$url
	 * @param	string	$type
	 * @return	bool
	 */
	public static function hasAsset($url, $type = null)
	{
		foreach (self::$_assets as $asset) {
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
	public static function hasNamedAsset($name)
	{
		foreach (self::$_assets as $asset) {
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
	public static function removeAsset($url, $type = null)
	{
		for ($i = 0, $c = count(self::$_assets); $i < $c; $i++) {
			if (self::$_assets[$i]['url'] == $url && ($type === null || self::$_assets[$i]['type'] == $type)) {
				unset(self::$_assets[$i]);
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
	public static function removeNamedAsset($name)
	{
		for ($i = 0, $c = count(self::$_assets); $i < $c; $i++) {
			if (self::$_assets[$i]['name'] == $name) {
				unset(self::$_assets[$i]);
			}
		}
	}
	
	/**
	 * Returns a list of assets according to the filter
	 * 
	 * @param	string	$type	Null for all
	 * @return 	array
	 */
	public static function getAssets($type = null)
	{
		$assets = array();
		foreach (self::$_assets as $asset) {
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
	public static function addStyle($url, $dependencies = array())
	{
		self::addAsset($url, self::CSS, $dependencies);
	}
	
	/**
	 * Returns all assets filenames of type text/css
	 * 
	 * @return 	array
	 */
	public static function getStyles()
	{
		return self::getAssets(self::CSS);
	}
	
	/**
	 * Renders the link tag for styles
	 * 
	 * @return 	string
	 */
	public static function renderStyles()
	{
		$html = '';
		foreach (self::getStyles() as $url) {
			$html .= sprintf('<link rel="stylesheet" type="text/css" href="%s" />' . "\n", self::_formatUrl($url));
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
	public static function addScript($url, $dependencies = array())
	{
		self::addAsset($url, self::JS, $dependencies);
	}
	
	/**
	 * Returns all assets filenames of type text/javascript
	 * 
	 * @return 	array
	 */
	public static function getScripts()
	{
		return self::getAssets(self::JS);
	}
	
	/**
	 * Renders the script tag for scripts
	 * 
	 * @return 	string
	 */
	public static function renderScripts()
	{
		$html = '';
		foreach (self::getScripts() as $url) {
			$html .= sprintf('<script type="text/javascript" src="%s"></script>' . "\n", self::_formatUrl($url));
		}
		return $html;
	}
	
	/**
	 * Renders all assets
	 * 
	 * @return string
	 */
	public static function render()
	{
		return self::renderStyles() . self::renderScripts();
	}
	
	/**
	 * Returns an asset type depending on its filename
	 * 
	 * @param	string	$url
	 * @param	string	$userType
	 * @return	string
	 */
	private static function _getAssetType($url, $userType = null)
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
	private static function _formatUrl($url)
	{
		if (self::$_urlCallback === null) {
			return $url;
		}
		return call_user_func(self::$_urlCallback, $url);
	}
}