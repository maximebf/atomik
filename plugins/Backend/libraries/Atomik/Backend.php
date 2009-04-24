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
			'submenu' => array()
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
		foreach (self::$_menu as $name => $item) {
			if (self::isCurrentMenu($name)) {
				return $item;
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
	public static function isCurrentMenu($name)
	{
		$pattern = self::$_menu[$name]['action'] . '/*';
		return Atomik::uriMatch($pattern, Atomik::get('backend/full_request_uri'));
	}
}