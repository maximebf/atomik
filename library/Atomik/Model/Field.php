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

/** Atomik_Db_Type */
require_once 'Atomik/Db/Type.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Field
{
	/** @var string */
	protected $_name;
	
	/** @var string */
	protected $_columnName;
	
	/** @var Atomik_Db_Type_Abstract */
	protected $_type;
	
	/** @var bool */
	protected $_required = false;
    
	/** @var array of Atomik_Model_Validator */
    protected $_validators = array();
    
    /**
     * @param string $name
     * @param string|Atomik_Db_Type_Abstract $type
     * @return Atomik_Model_Field
     */
    public static function factory($name, $type, $length = null)
    {
        if (is_string($type)) {
            $type = Atomik_Db_Type::factory($type, $length);
        }
        return new Atomik_Model_Field($name, $type);
    }
    
    /**
     * @param string $name
     * @param Atomik_Db_Type_Abstract $type
     */
    public function __construct($name, Atomik_Db_Type_Abstract $type)
    {
        $this->setName($name);
        $this->setType($type);
    }
    
	/**
	 * @param string $name
	 */
    public function setName($name)
    {
        $this->_name = $name;
        if ($this->_columnName === null) {
            $this->_columnName = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\1', $name));
        }
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
	/**
	 * @param string $name
	 */
    public function setColumnName($name)
    {
        $this->_columnName = $name;
    }
    
    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->_columnName;
    }
    
    /**
     * @param Atomik_Db_Type_Abstract $type
     */
    public function setType(Atomik_Db_Type_Abstract $type)
    {
        $this->_type = $type;
    }
    
    /**
     * @return Atomik_Db_Type_Abstract
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * @param bool $required
     */
    public function setRequired($required = true)
    {
        $this->_required = $required;
    }
    
    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->_required;
    }
    
    /**
     * @param array $validators array of Atomik_Model_Validator
     */
    public function setValidators($validators)
    {
        $this->_validators = array();
        array_map(array($this, 'addValidator'), $validators);
    }
    
    /**
     * @param Atomik_Model_Validator $validator
     */
    public function addValidator(Atomik_Model_Validator $validator)
    {
        $this->_validators[] = $validator;
    }
    
    /**
     * @return array of Atomik_Model_Validator
     */
    public function getValidators()
    {
        return $this->_validators;
    }
    
    /**
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if ($this->_required && empty($value)) {
            $this->_validationMessage = "Missing value for {$this->_name}";
            return false;
        }
        
        foreach ($this->_validators as $validator) {
            if (!$validator->isValid($value)) {
                $this->_validationMessage = $validator->getValidationMessage();
                return false;
            }
        }
        return true;
    }
	
	/**
	 * @return string
	 */
	public function getValidationMessage()
	{
	    return $this->_validationMessage;
	}
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->_name;
    }
}