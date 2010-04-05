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

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model_Descriptor_Property
{
	/** @var string */
	protected $_name;
	
	/** @var bool */
	protected $_inherited = false;
	
	/** @var bool */
	protected $_required = false;
    
	/** @var array of Atomik_Model_Validator */
    protected $_validators = array();
    
	/**
	 * @param string $name
	 */
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * @param bool $inherited
     */
    public function setInherited($inherited)
    {
        $this->_inherited = $inherited;
    }
    
    /**
     * @return bool
     */
    public function isInherited()
    {
        return $this->_inherited;
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
	
	public function __toString()
	{
	    return $this->_name;
	}
}