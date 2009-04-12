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
 * @subpackage Config
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Config_Backend_Interface */
require_once 'Atomik/Config/Backend/Interface.php';

/**
 * @package Atomik
 * @subpackage Config
 */
class Atomik_Config_Backend_Factory
{
	/**
	 * Creates an instance of a backend
	 * 
	 * @param 	string|objet 	$name	The last part of the backend name if it starts with Atomik_Config_Backend_ or a class name
	 * @return 	Atomik_Config_Backend_Interface
	 */
	public static function factory($name, $constructorArgs = array())
	{
		$className = 'Atomik_Config_Backend_' . ucfirst($name);
		if (!class_exists($className)) {
			$className = $name;
			if (!class_exists($className)) {
				require_once 'Atomik/Config/Exception.php';
				throw new Atomik_Config_Exception('No config backend named ' . $name . ' were found');
			}
		}
		
		$class = new ReflectionClass($className);
		return $class->newInstanceArgs($constructorArgs);
	}
}