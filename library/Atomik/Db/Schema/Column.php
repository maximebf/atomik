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
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Type_Abstract */
require_once 'Atomik/Db/Type/Abstract.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Schema_Column
{
    /** @var string */
	protected $_name;
	
	/** @var Atomik_Db_Type_Abstract */
	protected $_type;
	
	/** @var string */
	protected $_defaultValue;
	
	/** @var bool */
	protected $_nullable = true;
	
	/** @var array */
	protected $_options = array();
	
	/**
	 * @param string $name
	 * @param Atomik_Db_Type_Abstract $type
	 * @param array $options
	 */
	public function __construct($name, Atomik_Db_Type_Abstract $type, array $options = array())
	{
		$this->_name = $name;
		$this->_type = $type;
		$this->_options = $options;
	}
	
	/**
	 * Sets the column's name
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
	    $this->_name = $name;
	}
	
	/**
	 * Returns the column's name
	 * 
	 * @return string
	 */
	public function getName()
	{
	    return $this->_name;
	}
	
	/**
	 * Sets the column's type
	 * 
	 * @param Atomik_Db_Type_Abstract $type
	 */
	public function setType(Atomik_Db_Type_Abstract $type)
	{
	    $this->_type = $type;
	}
	
	/**
	 * Returns the column's type
	 * 
	 * @return Atomik_Db_Type_Abstract
	 */
	public function getType()
	{
	    return $this->_type;
	}
	
	/**
	 * Sets the column's default value
	 * 
	 * @param string $value
	 */
	public function setDefaultValue($value)
	{
	    $this->_defaultValue = $value;
	}
	
	/**
	 * @return string
	 */
	public function getDefaultValue()
	{
	    return $this->_defaultValue;
	}
	
	/**
	 * Sets whether the column can be null
	 * 
	 * @param bool $nullable
	 */
	public function setNullable($nullable)
	{
	    $this->_nullable = $nullable;
	}
	
	/**
	 * @return bool
	 */
	public function isNullable()
	{
	    return $this->_nullable;
	}
	
	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
	    $this->_options = $options;
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setOption($name, $value)
	{
	    $this->_options[$name] = $value;
	}
	
	/**
	 * @return array
	 */
	public function getOptions()
	{
	    return $this->_options;
	}
}