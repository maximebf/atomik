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
 * @subpackage Model
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model_Field_Abstract */
require_once 'Atomik/Model/Field/Abstract.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Field_Factory
{
	/**
	 * Creates an instance of a field
	 * 
	 * @param 	string|objet 	$name		The last part of the field name if it starts with Atomik_Model_Field_ or a class name
	 * @return 	Atomik_Model_Field_Abstract
	 */
	public static function factory($type, $name, $options = array())
	{
		$className = 'Atomik_Model_Field_' . ucfirst($type);
		if (!class_exists($className)) {
			$className = $type;
			if (!class_exists($className)) {
				require_once 'Atomik/Model/Field.php';
				return new Atomik_Model_Field($name, $type, $options);
			}
		}
		
		return new $className($name, $options);
	}
}