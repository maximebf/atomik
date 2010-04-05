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

/** Atomik_Model_Query_Filter_Interface */
require_once 'Atomik/Model/Query/Filter/Interface.php';

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model_Query_Filter_Field implements Atomik_Model_Query_Filter_Interface
{
    /** @var Atomik_Model_Descriptor */
	protected $_descriptor;
	
	/** @var string */
	protected $_field;
	
	/** @var string */
	protected $_value;
	
	/**
	 * @param mixed $descriptor
	 * @param mixed $field
	 * @param string $value
	 */
	public function __construct($descriptor, $field, $value = null)
	{
		$this->setDescriptor($descriptor);
		$this->setField($field);
		$this->setValue($value);
	}
	
	/**
	 * Sets the targeted descriptor
	 * 
	 * @param mixed $descriptor
	 */
	public function setDescriptor($descriptor)
	{
	    $this->_descriptor = Atomik_Model_Descriptor::factory($descriptor);
	}
	
	/**
	 * Returns the targeted descriptor
	 * 
	 * @return Atomik_Model_Descriptor
	 */
	public function getDescriptor()
	{
		return $this->_descriptor;
	}
	
	/**
	 * Sets the field that will be targeted.
	 * Must be part of the descriptor
	 * 
	 * @param mixed $field
	 */
	public function setField($field)
	{
	    $this->_field = (string) $field;
	}
	
	/**
	 * Returns the targeted field
	 * 
	 * @return Atomik_Model_Field
	 */
	public function getField()
	{
		return $this->_field;
	}
	
	/**
	 * Sets the value to match against the field
	 * 
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->_value = $value;
	}
	
	/**
	 * Returns the value to match against the field
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->_value;
	}
	
	/**
	 * Returns the sql column name for the targeted field
	 * 
	 * @return string
	 */
	protected function _getSqlColumn()
	{
	    return sprintf('%s.%s', 
	        $this->_descriptor->getTableName(),
	        $this->_field);
	}
}