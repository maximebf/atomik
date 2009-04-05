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
 * @subpackage Form
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Form_Field_Abstract */
require_once 'Atomik/Form/Field/Abstract.php';

/**
 * @package Atomik
 * @subpackage Form
 */
abstract class Atomik_Form_Field_Abstract
{
	/**
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @var string
	 */
	protected $_label;
	
	/**
	 * @var string
	 */
	protected $_value = '';
	
	/**
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * @var array
	 */
	protected $_validationMessages = array();
	
	/**
	 * Constructor
	 * 
	 * @param	string	$name
	 * @param	string	$label
	 * @param	array	$options
	 */
	public function __construct($name, $label = null, $options = array())
	{
		$this->setLabel($label);
		$this->setName($name);
		$this->setOptions($options);
	}
	
	/**
	 * Sets the name of the field
	 * 
	 * @param	string	$name
	 */
	public function setName($name)
	{
		$this->_name = $name;
		if ($this->_label === null) {
			$this->_label = $name;
		}
	}
	
	/**
	 * Returns the name of the field
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Sets the name of the field
	 * 
	 * @param	string	$label
	 */
	public function setLabel($label)
	{
		$this->_label = $label;
	}
	
	/**
	 * Returns the value of the field
	 * 
	 * @return string
	 */
	public function getLabel()
	{
		return $this->_label;
	}
	
	/**
	 * Sets the value of the field
	 * 
	 * @param	string	$value
	 */
	public function setValue($value)
	{
		$this->_value = $value;
	}
	
	/**
	 * Returns the value of the field
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->_value;
	}
	
	/**
	 * Sets all options
	 *
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$this->_options = (array) $options;
	}
	
	/**
	 * Sets an option
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setOption($name, $value)
	{
		$this->_options[$name] = $value;
	}
	
	/**
	 * Checks if an option exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasOption($name)
	{
		return array_key_exists($name, $this->_options);
	}
	
	/**
	 * Returns an option
	 *
	 * @param string $name
	 * @param mixed $default OPTIONAL Default value if the key is not found
	 * @return mixed
	 */
	public function getOption($name, $default = null)
	{
		if (!array_key_exists($name, $this->_options)) {
			return $default;
		}
		return $this->_options[$name];
	}
	
	/**
	 * Returns all options
	 * 
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
	
	/**
	 * Returns all options as an html attribute string
	 * 
	 * @param	array	$filter		Filters which options to include or exclude
	 * @param	bool	$exclude	Whether to include or exclude options from the filter
	 * @return	string
	 */
	public function getOptionsAsAttributeString($fitler = null, $exclude = false)
	{
		$string = '';
		foreach ($this->_options as $name => $value) {
			if (!empty($filter) && ((!$exclude && !in_array($name, $filter)) || ($exclude && in_array($name, $filter)))) {
				continue;
			}
			$string .= ' ' . $name . '="' . $value . '"';
		}
		return trim($string);
	}
	
	/**
	 * Checks if the field's value is valid
	 * 
	 * @return bool
	 */
	public function isValid()
	{
		return true;
	}
	
	/**
	 * Returns the messages generated during the last validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->_validationMessages;
	}
	
	/**
	 * Renders the field
	 * 
	 * @return string
	 */
	abstract public function render();
	
	/**
	 * @see Atomik_Form_Field_Abstract::render()
	 */
	public function __toString()
	{
		return $this->render();
	}
}