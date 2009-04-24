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
 * @subpackage Backend
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * Backend main class
 * 
 * @package Atomik
 * @subpackage Backend
 */
class Atomik_Backend_Layout
{
	const CSS = 'text/css';
	const JS = 'text/javascript';
	
	/**
	 * @var array
	 */
	private static $_assets = array();
	
	/**
	 * @var array
	 */
	private static $_placeholders = array();
	
	/**
	 * Adds a new asset file
	 * 
	 * @see Atomik::pluginAsset()
	 * @param	string	$filename
	 * @param	string	$type
	 * @param	string	$plugin		Can be app, in this case Atomik::asset() will be used
	 * @param 	bool	$allowTwice	Whether the asset can be added twice
	 */
	public static function addAsset($filename, $type = 'text/css', $plugin = 'backend', $allowTwice = false)
	{
		if (!$allowTwice && self::hasAsset($filename, $type, $plugin)) {
			return;
		}
		
		self::$_assets[] = array('filename' => $filename, 'type' => $type, 'plugin' => $plugin);
	}
	
	/**
	 * Checks if an asset exists
	 * 
	 * @param	string	$filename
	 * @param	string	$type
	 * @param	string	$plugin
	 */
	public static function hasAsset($filename, $type = 'text/css', $plugin = 'backend')
	{
		foreach (self::$_assets as $asset) {
			if ($asset['plugin'] == $plugin && $asset['type'] == $type && $asset['filename'] == $filename) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Removes an asset
	 * 
	 * @param	string	$filename
	 * @param	string	$type
	 * @param	string	$plugin
	 */
	public static function removeAsset($filename, $type = 'text/css', $plugin = 'backend')
	{
		for ($i = 0, $c = count(self::$_assets); $i < $c; $i++) {
			if (self::$_assets[$i]['plugin'] == $plugin && self::$_assets[$i]['type'] == $type 
					&& self::$_assets[$i]['filename'] == $filename) {
				unset(self::$_assets[$i]);
				return;
			}
		}
	}
	
	/**
	 * Returns a list of assets according to the filter
	 * 
	 * @param	string	$type	Null for all
	 * @param	string	$plugin	Null for all
	 * @return 	array
	 */
	public static function getAssets($type = null, $plugin = null)
	{
		$assets = array();
		foreach (self::$_assets as $asset) {
			if (($type === null || $asset['type'] == $type) && ($plugin === null || $asset['plugin'] == $plugin)) {
				$assets[] = $asset;
			}
		}
		return $assets;
	}
	
	/**
	 * Returns assets filenames for assets matching the filters
	 * 
	 * @param	string	$type	Null for all
	 * @param	string	$plugin	Null for all
	 * @return 	array
	 */
	public static function getAssetsFilenames($type = null, $plugin = null)
	{
		$assets = self::getAssets($type, $plugin);
		$files = array();
		
		foreach ($assets as $asset) {
			if ($asset['plugin'] == 'app') {
				$files[] = Atomik::asset($asset['filename']);
			} else {
				$files[] = Atomik::pluginAsset($asset['filename'], $asset['plugin']);
			}
		}
		
		return $files;
	}
	
	/**
	 * Adds a new asset of type text/css
	 * 
	 * @see Atomik_Backend_Layout::addAsset()
	 * @param 	string	$filename
	 * @param 	string	$plugin
	 */
	public static function addStyle($filename, $plugin = 'backend')
	{
		self::addAsset($filename, self::CSS, $plugin);
	}
	
	/**
	 * Returns all assets filenames of type text/css
	 * 
	 * @param 	string	$plugin
	 * @return 	array
	 */
	public static function getStyles($plugin = null)
	{
		return self::getAssetsFilenames(self::CSS, $plugin);
	}
	
	/**
	 * Adds a new asset of type text/javascript
	 * 
	 * @see Atomik_Backend_Layout::addAsset()
	 * @param 	string	$filename
	 * @param 	string	$plugin
	 */
	public static function addScript($filename, $plugin = 'backend')
	{
		self::addAsset($filename, self::JS, $plugin);
	}
	
	/**
	 * Returns all assets filenames of type text/javascript
	 * 
	 * @param 	string	$plugin
	 * @return 	array
	 */
	public static function getScripts($plugin = null)
	{
		return self::getAssetsFilenames(self::JS, $plugin);
	}
	
	/**
	 * Renders a view in the specified placeholder
	 * 
	 * @param 	string	$name	Placeholder name
	 * @param 	string	$view	View name
	 * @param 	array	$vars	View variables
	 */
	public static function renderInPlaceholder($name, $view, $vars = array())
	{
		if (!isset(self::$_placeholders[$name])) {
			self::$_placeholders[$name] = '';
		}
		self::$_placeholders[$name] .= Atomik::render($view, $vars);
	}
	
	/**
	 * Appends a string to a placeholder's content
	 * 
	 * @param 	string	$name	Placeholder name
	 * @param 	string	$content
	 */
	public static function addToPlaceholder($name, $content)
	{
		if (!isset(self::$_placeholders[$name])) {
			self::$_placeholders[$name] = '';
		}
		self::$_placeholders[$name] .= $content;
	}
	
	/**
	 * Resets a placeholder
	 * 
	 * @param 	string	$name	Placeholder name
	 */
	public static function resetPlaceholder($name)
	{
		self::$_placeholders[$name] = '';
	}
	
	/**
	 * Returns the content of a placeholder
	 * 
	 * @param 	string	$name	Placeholder name
	 * @return 	string
	 */
	public static function renderPlaceholder($name)
	{
		if (!isset(self::$_placeholders[$name])) {
			return '';
		}
		return self::$_placeholders[$name];
	}
}