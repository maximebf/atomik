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

/** Atomik_Model_Descriptor_Builder */
require_once 'Atomik/Model/Descriptor/Builder.php';

/** Atomik_Model_Field */
require_once 'Atomik/Model/Field.php';

/** Atomik_Model_Association */
require_once 'Atomik/Model/Association.php';

/** Atomik_Model_Session */
require_once 'Atomik/Model/Session.php';

/**
 * A model descriptor can be used to programatically build models
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor
{
    const INHERITANCE_ABSTRACT = 'abstract';
    const INHERITANCE_JOINED = 'joined';
    
	/** @var string */
	protected $_name;
	
	/** @var string */
	protected $_className;
	
	/** @var string */
	protected $_tableName;
	
	/** @var string */
	protected $_inheritanceType = 'abstract';
	
	/** @var Atomik_Model_Session */
	protected $_session;
	
	/** @var Atomik_Model_Descriptor */
	protected $_parentModelDescriptor;
	
	/** @var array */
	protected $_fields = array();
	
	/** @var Atomik_Model_Field_Abstract */
	protected $_primaryKeyField;
	
	/** @var bool */
	protected $_autoPrimaryKey = true;
	
	/** @var array */
	protected $_associations = array();
	
	/** @var array */
	private static $_descriptors = array();
	
	/**
	 * Returns a descriptor instance for to the model of the specified name
	 * 
	 * @param string|objet $name
	 * @return Atomik_Model_Descriptor
	 */
	public static function factory($name)
	{
		if ($name instanceof Atomik_Model_Descriptor) {
			$name = $name->getName();
		}
		
		if ($name instanceof Atomik_Model) {
			$name = get_class($name);
		}
		
		if (isset(self::$_descriptors[$name])) {
			return self::$_descriptors[$name];
		}
		
		if (class_exists($name)) {
			$builder = new Atomik_Model_Descriptor_Builder();
			self::$_descriptors[$name] = $builder->build($name);
			return self::$_descriptors[$name];
		}
		
		require_once 'Atomik/Model/Descriptor/Exception.php';
		throw new Atomik_Model_Descriptor_Exception("No model descriptor named '$name' were found");
	}
	
	/**
	 * @param string $name
	 * @param array $metadata
	 */
	public function __construct($name, $className = null, $tableName = null)
	{
		$this->_name = $name;
		$this->_className = $className;
		$this->_tableName = $tableName === null ? strtolower($name) : $tableName;
		$this->setPrimaryKeyField();
		$this->setRepresentationField($this->_primaryKeyField);
	}
	
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
	 * @param string $name
	 */
	public function setClassName($name)
	{
	    $this->_className = $name;
	}
	
	/**
	 * @return string
	 */
	public function getClassName()
	{
	    return $this->_className;
	}
	
	/**
	 * @param string $name
	 */
	public function setTableName($name)
	{
	    $this->_tableName = $name;
	}
	
	/**
	 * @return string
	 */
	public function getTableName()
	{
	    return $this->_tableName;
	}
	
	/**
	 * @param string $type
	 */
	public function setInheritanceType($type)
	{
	    $this->_inheritanceType = $type;
	}
	
	/**
	 * @return string
	 */
	public function getInheritanceType()
	{
		return $this->_inheritanceType;
	}
	
	/**
	 * @param Atomik_Model_Session $session
	 */
	public function setSession(Atomik_Model_Session $session)
	{
	    $this->_session = $session;
	}
	
	/**
	 * @return Atomik_Model_Session
	 */
	public function getSession()
	{
	    if ($this->_session === null) {
	        $this->_session = Atomik_Model_Session::getInstance();
	    }
	    return $this->_session;
	}
	
	/**
	 * @param string|Atomik_Model_Descriptor $parentModel
	 */
	public function setParentModel($parentModel)
	{
		$parent = Atomik_Model_Descriptor::factory($parentModel);
		$type = $parent->getInheritanceType();
		
		switch($type) {
			case self::INHERITANCE_ABSTRACT:
				$this->_fields = array_merge($parent->getFields(), $this->_fields);
				foreach ($parent->getBehaviourBroker()->getBehaviours() as $behaviour) {
					$this->_behaviourBroker->addBehaviour(clone $behaviour);
				}
				foreach ($parent->getAssociations() as $assoc) {
					$this->addAssociation(clone $assoc);
				}
				break;
				
			case self::INHERITANCE_JOINED:
				$assoc = new Atomik_Model_Association_ManyToOne($this, 'parent', $parent);
				$this->addAssociation($assoc);
				break;
		}
		
		$this->_parentModelDescriptor = $parent;
	}
	
	/**
	 * @return bool
	 */
	public function hasParentModel()
	{
		return $this->_parentModelDescriptor !== null;
	}
	
	/**
	 * @return Atomik_Model_Descriptor
	 */
	public function getParentModel()
	{
		return $this->_parentModelDescriptor;
	}
	
	/**
	 * Resets all the fields
	 *
	 * @param array $fields
	 */
	public function setFields($fields = array())
	{
		$this->_fields = array();
		foreach ($fields as $field) {
			$this->addField($field);
		}
	}
	
	/**
	 * @param Atomik_Model_Field $field
	 */
	public function addField(Atomik_Model_Field $field)
	{
		$this->_fields[$field->getName()] = $field;
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasField($name)
	{
		return isset($this->_fields[(string) $name]);
	}
	
	/**
	 * @param string $name
	 * @return Atomik_Model_Field
	 */
	public function getField($name)
	{
		if (!isset($this->_fields[$name])) {
			return false;
		}
		return $this->_fields[$name];
	}
	
	/**
	 * @return array of Atomik_Model_Field
	 */
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * Sets the primary key field
	 * 
	 * If $field is null, will use or create a field named id
	 * 
	 * @param Atomik_Model_Field $field
	 */
	public function setPrimaryKeyField(Atomik_Model_Field $field = null)
	{
		$removeAutoKey = true;
		
		if ($field === null) {
			if ($this->_primaryKeyField !== null) {
				// primary key already defined
				return;
			}
			
			$field = Atomik_Model_Field::factory('id', 'int');
			$this->addField($field);
			$this->_autoPrimaryKey = true;
			$removeAutoKey = false;
		}
		
		if ($removeAutoKey && $this->_autoPrimaryKey) {
			unset($this->_fields['id']);
			$this->_autoPrimaryKey = false;
		}
		
		$this->_primaryKeyField = $field;
	}
	
	/**
	 * @return Atomik_Model_Field
	 */
	public function getPrimaryKeyField()
	{
		return $this->_primaryKeyField;
	}
	
	/**
	 * @param Atomik_Model_Field $field
	 */
	public function setRepresentationField($field)
	{
	    if (is_string($field)) {
	        $field = $this->getField($field);
	    }
	    $this->_representationField = $field;
	}
	
	/**
	 * @return Atomik_Model_Field
	 */
	public function getRepresentationField()
	{
	    return $this->_representationField;
	}
	
	/**
	 * Resets all the associations
	 *
	 * @param array $associations
	 */
	public function setAssociations($associations = array())
	{
		$this->_associations = array();
		foreach ($associations as $association) {
			$this->addAssociation($association);
		}
	}
	
	/**
	 * @param Atomik_Model_Association $association
	 */
	public function addAssociation(Atomik_Model_Association $assoc)
	{
		$this->_associations[$assoc->getName()] = $assoc;
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAssociation($name)
	{
		return isset($this->_associations[$name]);
	}
	
	/**
	 * @param string $name 
	 * @return Atomik_Model_Association
	 */
	public function getAssociation($name)
	{
		if (!isset($this->_associations[$name])) {
			return false;
		}
		return $this->_associations[$name];
	}
	
	/**
	 * @param string $fieldName
	 * @return Atomik_Model_Association
	 */
	public function getAssociationFromSourceField($fieldName)
	{
		foreach ($this->_associations as $association) {
			if ($association->getSourceFieldName() == (string) $fieldName) {
				return $association;
			}
		}
	}
	
	/**
	 * @param string $modelName
	 * @return array
	 */
	public function getAssociations($targetModel = null)
	{
		if ($targetModel === null) {
			return $this->_associations;
		}
		
		$associations = array();
		foreach ($this->_associations as $association) {
			if ($association->getTarget() == $targetModel) {
				    $associations[] = $association;
			}
		}
		return $associations;
	}
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	public function isFieldPartOfAssociation($fieldName)
	{
		return $this->getAssociationFromSourceField($fieldName) !== null;
	}
	
	/**
	 * @param Atomik_Model_Descriptor $descriptor
	 * @return bool
	 */
	public function isModelAssociated(Atomik_Model_Descriptor $descriptor)
	{
		foreach ($this->_associations as $association) {
			if ($association->getTarget() == $descriptor) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Creates a model instance
	 *
	 * @see Atomik_Model::__construct()
	 * @param 	array 			$values
	 * @param 	bool 			$new	
	 * @return 	Atomik_Model
	 */
	public function createInstance($values = array(), $new = true)
	{
		$className = $this->_className;
		if ($className === null) {
			/** Atomik_Model */
			require_once 'Atomik/Model.php';
			$className = 'Atomik_Model';
		}
		
		$data = array();
		foreach ($this->_fields as $field) {
		    if (isset($values[$field->getColumnName()])) {
		        $data[$field->getName()] = $field->getType()->filterInput(
		                                        $values[$field->getColumnName()]);
		    }
		}
		
		$this->getSession()->notify('BeforeCreateInstance', $this, $data, $new);
		$instance = new $className($data, $new, $this);
		$this->getSession()->notify('AfterCreateInstance', $this, $instance);
		
		return $instance;
	}
	
	/**
	 * Returns the descriptor name
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->_name;
	}
}