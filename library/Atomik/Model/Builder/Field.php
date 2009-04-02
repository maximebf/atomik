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

/** Atomik_Model_Options */
require_once 'Atomik/Model/Options.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder_Field extends Atomik_Model_Options
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var array
	 */
	protected $_validationMessages = array();
	
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
	 * Checks if the specified value is valid
	 *
	 * @param 	mixed $value
	 * @return 	bool
	 */
	public function isValid($value)
	{
		$isValid = true;
		$this->_validationMessages = array();
			
		if ($this->hasOption('validate')) {
			if(!preg_match($this->getOption('validate'), $value)) {
				$this->_validationMessages[] = $this->name . ' failed to validate because it '
											 . 'didn\'t match the regexp: ' . $this->getOption('validate');
				return false;
			}
			
			return true;
		}
		
		if ($this->hasOption('validate-with')) {
			$callback = $this->getOption('validate-with');
			if (!call_user_func($callback, $value)) {
				$this->_validationMessages[] = $this->name . ' failed to validate because '
											 . $this->getOption('validate-with') . '() returned false';
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Returns the messages generated during the validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->_validationMessages;
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