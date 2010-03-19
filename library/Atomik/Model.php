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

/** Atomik_Model_Descriptor */
require_once 'Atomik/Model/Descriptor.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model
{
	/** @var Atomik_Model_Descriptor */
	protected $_descriptor;
	
	/** @var bool */
	protected $_new = true;
	
	/**
	 * @param 	array 					$data
	 * @param 	bool 					$new	Whether the model is already saved or not
	 * @param	Atomik_Model_Descriptor	$descriptor
	 */
	public function __construct($data = array(), $new = true, Atomik_Model_Descriptor $descriptor = null)
	{
		$this->_new = $new;
		$this->_descriptor = $descriptor;
		
		if ($descriptor === null) {
		    $this->_setupDescriptor();
		}
		
		foreach ($data as $key => $value) {
		    $this->_set($key, $value);
		}
	}
	
	protected function _setupDescriptor()
	{
		$this->_descriptor = Atomik_Model_Descriptor::factory($this);
	}
	
	/**
	 * @return Atomik_Model_Descriptor
	 */
	public function getDescriptor()
	{
		return $this->_descriptor;
	}
	
	/**
	 * @return Atomik_Model_Session
	 */
	public function getSession()
	{
		return $this->_descriptor->getSession();
	}
	
	/**
	 * @return bool
	 */
	public function isNew()
	{
		return $this->_new;
	}
	
	/**
	 * @param mixed $value
	 */
	public function setPrimaryKey($value)
	{
		$this->{$this->_descriptor->getPrimaryKeyField()->getName()} = $value;
	}
	
	/**
	 * @return mixed
	 */
	public function getPrimaryKey()
	{
		return $this->{$this->_descriptor->getPrimaryKeyField()->getName()};
	}
	
	/**
	 * @param string $fieldName
	 * @param mixed $value
	 */
	public function _set($fieldName, $value)
	{
	    if (!$this->_descriptor->hasField($fieldName) &&
	        !$this->_descriptor->hasAssociation($fieldName)) {
	            return;
	    }
		
		$this->{$fieldName} = $value;
	}
	
	/**
	 * @param string $fieldName
	 * @return mixed
	 */
	public function _get($fieldName)
	{
	    if ($this->_descriptor->hasAssociation($fieldName) && 
	        $this->{$fieldName} === null) {
                $this->_descriptor->getAssociation($fieldName)->load($this);
	    }
	    
        return $this->{$fieldName};
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 */
	public function __call($method, $args)
	{
	    if (!preg_match('/^(get|set)(.+)$/', $method, $matches)) {
	        return;
	    }
	    
	    $accessor = $matches[1];
	    $property = $matches[2];
	    $property = strtolower($property{0}) . substr($property, 1);
	    
	    if (!property_exists($this, $property)) {
	        return;
	    }
	    
	    if ($accessor == 'get') {
	        return $this->_get($property);
	    } else if ($accessor == 'set') {
	        return $this->_set($property, $args[0]);
	    }
	}
	
	public function isValid()
	{
		return $this->getSession()->isValid($this);
	}
	
	public function save($validate = true)
	{
		$this->getSession()->save($this, $validate);
		$this->_new = false;
	}
	
	public function delete()
	{
		$this->getSession()->delete($this);
		$this->_new = true;
	}
	
	/**
	 * @return array
	 */
	public function toArray($includeAssocs = false)
	{
		$data = array();
		$fields = $this->_descriptor->getFields();
		
		foreach ($fields as $field) {
			$data[$field->getName()] = $this->_get($field->getName());
		}
		
		return $data;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$reprField = $this->_descriptor->getRepresentationField();
		return $this->_get($reprField->getName());
	}
	
	public function __clone()
	{
		$this->_new = true;
		$this->setPrimaryKey(null);
	}
}
