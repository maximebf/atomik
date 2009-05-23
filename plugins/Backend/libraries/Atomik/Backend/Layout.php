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
	/**
	 * @var array
	 */
	private static $_placeholders = array();
	
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