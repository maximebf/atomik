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
 * @package Atomik
 * @subpackage Backend
 */
class Atomik_Backend_Assets
{
	/**
	 * Registers a named asset
	 * 
	 * @param	string			$name
	 * @param	string|array	$url
	 * @param 	string			$type
	 * @param	array			$dependencies
	 */
	public static function registerNamedAsset($name, $url, $plugin = null, $type = null, $dependencies = array())
	{
		if (is_array($url)) {
			return Atomik_Assets::registerNamedAsset($name, $url);
		}
		return Atomik_Assets::registerNamedAsset($name, self::assetUrl($url, $plugin), $type, $dependencies);
	}
	
	/**
	 * Creates an asset for the backend
	 * 
	 * @param	string	$url
	 * @param	string	$plugin
	 * @param 	string	$type
	 */
	public static function createAsset($url, $plugin = null, $type = null, $dependencies = array(), $name = null)
	{
		return Atomik_Assets::createAsset(self::assetUrl($url, $plugin), $type, $dependencies, $name);
	}
	
	/**
	 * Adds a new asset of type text/css
	 * 
	 * @see Atomik_Assets::addStyle()
	 * @param 	string	$url
	 * @param 	string	$plugin
	 */
	public static function addStyle($url, $plugin = null, $dependencies = array())
	{
		return Atomik_Assets::addAsset(self::assetUrl($url, $plugin), Atomik_Assets::CSS, $dependencies);
	}
	
	/**
	 * Adds a new asset of type text/javascript
	 * 
	 * @see Atomik_Assets::addScript()
	 * @param 	string	$url
	 * @param 	string	$plugin
	 */
	public static function addScript($url, $plugin = null, $dependencies = array())
	{
		return Atomik_Assets::addAsset(self::assetUrl($url, $plugin), Atomik_Assets::JS, $dependencies);
	}
	
	/**
	 * Returns the full url of an asset file
	 * 
	 * @param	string	$url
	 * @param	string	$plugin
	 * @return	string
	 */
	public static function assetUrl($url, $plugin = null)
	{
		if ($plugin == 'app') {
			return Atomik::asset($url);
		}
		return Atomik::pluginAsset($url, empty($plugin) ? 'backend' : $plugin);
	}
}