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
	 * Creates an asset for the backend
	 * 
	 * @param	string	$url
	 * @param	string	$plugin
	 * @param 	string	$type
	 */
	public static function createAsset($url, $plugin = 'backend', $type = null, $name = null)
	{
		return Atomik_Assets::createAsset(self::assetUrl($url, $plugin), $type, $name);
	}
	
	/**
	 * Adds a new asset of type text/css
	 * 
	 * @see Atomik_Backend_Layout::addAsset()
	 * @param 	string	$url
	 * @param 	string	$plugin
	 */
	public static function addStyle($url, $plugin = 'backend')
	{
		Atomik_Assets::addAsset(self::assetUrl($url, $plugin), Atomik_Assets::CSS);
	}
	
	/**
	 * Adds a new asset of type text/javascript
	 * 
	 * @see Atomik_Backend_Layout::addAsset()
	 * @param 	string	$url
	 * @param 	string	$plugin
	 */
	public static function addScript($url, $plugin = 'backend')
	{
		Atomik_Assets::addAsset(self::assetUrl($url, $plugin), Atomik_Assets::JS);
	}
	
	/**
	 * Returns the full url of an asset file
	 * 
	 * @param	string	$url
	 * @param	string	$plugin
	 * @return	string
	 */
	public static function assetUrl($url, $plugin = 'backend')
	{
		if ($plugin == 'app') {
			return Atomik::asset($url);
		}
		return Atomik::pluginAsset($url, $plugin);
	}
}