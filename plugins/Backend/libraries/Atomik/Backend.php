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
class Atomik_Backend
{
	/**
	 * Tabs
	 *
	 * @var array
	 */
	protected static $tabs = array();
	
	/**
	 * Adds a tab
	 *
	 * @param string $text
	 * @param string $plugin
	 * @param string $action
	 * @param string $position OPTIONAL (default left)
	 * @param bool $selectOnPlugin OPTIONAL The tabs is activated for any action of the plugin
	 */
	public static function addTab($text, $plugin, $action, $position = 'left', $selectOnPlugin = true)
	{
		$url = strtolower($plugin) . '/' . ltrim($action, '/');
		
		self::$tabs[] = array(
			'text' => $text,
			'plugin' => $plugin,
			'action' => $action,
			'position' => $position,
			'url' => $url,
			'selectOnPlugin' => $selectOnPlugin
		);
	}
	
	/**
	 * Removes all tabs
	 */
	public static function removeAllTabs()
	{
		self::$tabs = array();
	}
	
	/**
	 * Gets all tabs
	 *
	 * @return array
	 */
	public static function getTabs()
	{
		return self::$tabs;
	}
	
	/**
	 * Gets the current active tab
	 *
	 * @return array
	 */
	public static function getCurrentTab()
	{
		foreach (self::$tabs as $tab) {
			if (self::isCurrentTab($tab)) {
				return $tab;
			}
		}
		return null;
	}
	
	/**
	 * Checks if a tag is the current active one
	 *
	 * @param array $tab
	 * @return bool
	 */
	public static function isCurrentTab($tab)
	{
		$isPlugin = strtolower($tab['plugin']) == strtolower(Atomik::get('backend/plugin'));
		if ($tab['selectOnPlugin'] && $isPlugin) {
			return true;
		} else if (!$isPlugin) {
			return false;
		}
		
		$uri = strtolower(Atomik::get('request_uri'));
		$match = strtolower($tab['action']);
		
		return strlen($uri) >= strlen($match) && substr($uri, 0, strlen($match)) == $match;
	}
}