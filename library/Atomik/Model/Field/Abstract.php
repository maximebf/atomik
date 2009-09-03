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

/** Atomik_Options */
require_once 'Atomik/Options.php';

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model_Field_Abstract extends Atomik_Options
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$name
	 * @param 	array	$options
	 */
	public function __construct($name, $options = array())
	{
		$this->name = $name;
		$this->setOptions($options);
	}
	
	/**
	 * Filters the data from the model to the database
	 * 
	 * @param mixed $input
	 * @return mixed
	 */
	public function filterOutput($output) 
	{
		return $output;
	}
	
	/**
	 * Filters the data from the database to the model
	 * 
	 * @param mixed $output
	 * @return mixed
	 */
	public function filterInput($input)
	{
		return $input;
	}
	
	/**
	 * Returns an array where the first item is the sql type name and the second the length
	 * 
	 * @return array
	 */
	abstract public function getSqlType();
	
	/**
	 * Returns a form field
	 * 
	 * @return Atomik_Form_Field_Interface
	 */
	public function getFormField()
	{
		if (($type = $this->getOption('form-field', null)) !== null) {
			return Atomik_Form_Field_Factory::factory($type, $this->name, $this->getOptions('form-'));
		}
			
		return $this->getDefaultFormField();
	}
	
	/**
	 * Returns the default form field
	 * 
	 * @return Atomik_Form_Field_Interface
	 */
	public function getDefaultFormField()
	{
		return Atomik_Form_Field_Factory::factory('Input', $this->name, $this->getOptions('form-'));
	}
	
	/**
	 * Returns the field label
	 * 
	 * @return string
	 */
	public function getLabel()
	{
		$default = strtolower(str_replace('_', ' ', preg_replace('/(?<=\\w)([A-Z])/', ' \1', $this->name)));
		return $this->getOption('label', $default);
	}
	
	/**
	 * Returns a displayable string representing the value
	 * 
	 * @param 	mixed $value
	 * @return	string
	 */
	public function render($value)
	{
		return $value;
	}
	
	/**
	 * Returns the name of the field
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}