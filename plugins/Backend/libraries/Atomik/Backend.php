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

/** Atomik_Backend_Layout */
require_once 'Atomik/Backend/Layout.php';

/**
 * Backend main class
 * 
 * @package Atomik
 * @subpackage Backend
 */
class Atomik_Backend
{
	/**
	 * @var array
	 */
	protected static $_menu = array();
	
	public static function pluginAction($plugin, $action)
	{
		return Atomik::get('backend/base_action') . '/' . $plugin . '/' . $action;
	}
	
	public static function pluginUrl($plugin, $action, $params = array(), $useIndex = true)
	{
		return Atomik::url(self::pluginAction($plugin, $action), $params, $useIndex);
	}
	
	/**
	 * Adds a new top menu item
	 * 
	 * @param 	string	$name
	 * @param 	string	$label
	 * @param 	string	$action
	 * @param 	array	$submenus	An array where keys are labels and values are actions
	 * @param 	string	$position	Either right or left
	 */
	public static function addMenu($name, $label, $action, $submenus = array(), $position = 'left')
	{
		self::$_menu[$name] = array(
			'name' => $name,
			'label' => $label,
			'action' => trim($action, '/'),
			'position' => $position,
			'submenu' => isset(self::$_menu[$name]) ? self::$_menu[$name]['submenu'] : array()
		);
		
		foreach ($submenus as $submenuLabel => $submenuAction) {
			self::addSubMenu($name, $submenuLabel, $submenuAction);
		}
	}
	
	/**
	 * Adds a sub menu item
	 * 
	 * @param	string	$menuName	Parent menu name
	 * @param 	string	$label
	 * @param 	string	$action
	 */
	public static function addSubMenu($menuName, $label, $action)
	{
		if (!isset(self::$_menu[$menuName])) {
			return;
		}
		
		self::$_menu[$menuName]['submenu'][$label] = $action;
	}
	
	/**
	 * Removes all menu items
	 */
	public static function resetMenu()
	{
		self::$_menu = array();
	}
	
	/**
	 * Returns all menu items
	 * 
	 * @return array
	 */
	public static function getMenu()
	{
		return self::$_menu;
	}
	
	/**
	 * Returns the current active menu item
	 *
	 * @return array
	 */
	public static function getCurrentMenu()
	{
		$url = Atomik::get('backend/full_request_uri');
		$currentMenu = null;
		
		foreach (self::$_menu as $name => $item) {
			if ($item['action'] == $url) {
				return $item;
			} else if (Atomik::uriMatch($item['action'] . '/*', $url)) {
				$currentMenu = $item;
			} else {
				foreach ($item['submenu'] as $subLabel => $subAction) {
					if (Atomik::uriMatch($subAction . '/*', $url)) {
						return $item;
					}
				}
			}
		}
		
		return $currentMenu;
	}
}