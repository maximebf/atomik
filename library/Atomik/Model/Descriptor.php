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

/** Atomik_Model_Persister */
require_once 'Atomik/Model/Persister.php';

/** Atomik_Model_Descriptor_Builder */
require_once 'Atomik/Model/Descriptor/Builder.php';

/** Atomik_Model_Field */
require_once 'Atomik/Model/Field.php';

/** Atomik_Model_Association */
require_once 'Atomik/Model/Association.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor
{
    const INHERITANCE_NONE = 'none';
    const INHERITANCE_SINGLE = 'single';
    const INHERITANCE_JOINED = 'joined';
    
	/** @var string */
	protected $_name;
	
	/** @var string */
	protected $_tableName;
	
	/** @var Atomik_Db_Instance */
	protected $_db;
	
	/** @var Atomik_Model_Persister */
	protected $_persister;
	
	/** @var Atomik_Model_Hydrator */
	protected $_hydrator;
	
	/** @var string */
	protected $_inheritanceType = 'none';
	
	/** @var Atomik_Model_Descriptor */
	protected $_parent;
	
	/** @var array */
	protected $_mappedProperties = array();
	
	/** @var array of Atomik_Model_Field */
	protected $_fields = array();
	
	/** @var Atomik_Model_Field */
	protected $_identifierField;
	
	/** @var Atomik_Model_Field */
	protected $_descriminatorField;
	
	/** @var array of Atomik_Model_Association */
	protected $_associations = array();
	
	/** @var array of Atomik_Model_Behaviour */
	protected $_behaviours = array();
	
	/** @var array of Atomik_Model_EventListener */
	protected $_listeners = array();
	
	/** @var array of Atomik_Model_Descriptor */
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
		} else if (is_object($name)) {
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
	 * @param string $className
	 * @param string $tableName
	 */
	public function __construct($className, $tableName = null)
	{
	    if (!class_exists($className)) {
	        throw new Atomik_Model_Exception("Class '$className' not found");
	    }
	    
		$this->_name = $className;
		$this->_tableName = $tableName === null ? strtolower($name) : $tableName;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
	    return $this->_name;
	}
	
	/**
	 * Returns the name with the first letter lower cased
	 * 
	 * @return string
	 */
	public function getNameAsProperty()
	{
	    $name = $this->_name;
	    $name{0} = strtolower($name{0});
	    return $name;
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
	    if ($this->_parent !== null && 
	        $this->_parent->getInheritanceType() == self::INHERITANCE_SINGLE) {
	            return $this->_parent->getTableName();
	        }
	    return $this->_tableName;
	}
	
	/**
	 * Sets the associated database instance
	 * 
	 * @param Atomik_Db_Instance $db
	 */
	public function setDb(Atomik_Db $db)
	{
	    $this->_db = $db;
	}
	
	/**
	 * Returns the associated database instance
	 * 
	 * @return Atomik_Db_Instance
	 */
	public function getDb()
	{
	    if ($this->_db === null) {
	        $this->_db = Atomik_Db::getInstance();
	    }
	    return $this->_db;
	}
	
	/**
	 * @return Atomik_Model_Persister
	 */
	public function getPersister()
	{
		if ($this->_persister === null) {
	        if (!$this->hasParent()) {
	            require_once 'Atomik/Model/Persister/Standard.php';
	            $this->_persister  = new Atomik_Model_Persister_Standard($this);
	        } else {
	            switch ($this->getParent()->getInheritanceType()) {
	                case self::INHERITANCE_SINGLE:
        	            require_once 'Atomik/Model/Persister/Single.php';
        	            $this->_persister  = new Atomik_Model_Persister_Single($this);
	                    break;
	                case self::INHERITANCE_JOINED:
        	            require_once 'Atomik/Model/Persister/Joined.php';
        	            $this->_persister  = new Atomik_Model_Persister_Joined($this);
	                    break;
	            }
	        }
		}
		
		return $this->_persister;
	}
	
	/**
	 * @return Atomik_Model_Hydrator
	 */
	public function getHydrator()
	{
		if ($this->_hydrator === null) {
            $type = $this->hasParent() ? $this->getParent()->getInheritanceType() : $this->_inheritanceType;
            switch ($type) {
                case self::INHERITANCE_SINGLE:
    	            require_once 'Atomik/Model/Hydrator/Single.php';
    	            $this->_hydrator  = new Atomik_Model_Hydrator_Single($this);
                    break;
                case self::INHERITANCE_JOINED:
    	            require_once 'Atomik/Model/Hydrator/Joined.php';
    	            $this->_hydrator  = new Atomik_Model_Hydrator_Joined($this);
                    break;
                default:
    	            require_once 'Atomik/Model/Hydrator/Standard.php';
    	            $this->_hydrator  = new Atomik_Model_Hydrator_Standard($this);
            }
		}
		
		return $this->_hydrator;
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
	 * @param mixed $parentModel
	 */
	public function setParent($parentDescriptor)
	{
		$this->_parent = self::factory($parentDescriptor);
	}
	
	/**
	 * @return bool
	 */
	public function hasParent()
	{
		return $this->_parent !== null;
	}
	
	/**
	 * @return Atomik_Model_Descriptor
	 */
	public function getParent()
	{
		return $this->_parent;
	}
	
	/**
	 * Maps a property to a field or an association
	 * 
	 * @param Atomik_Model_Descriptor_Property $prop
	 */
	public function mapProperty(Atomik_Model_Descriptor_Property $prop)
	{
		$this->_mappedProperties[$prop->getName()] = $prop;
		
		if ($prop instanceof Atomik_Model_Field) {
		    $this->_fields[$prop->getName()] = $prop;
		} else if ($prop instanceof Atomik_Model_Association) {
		    $this->_associations[$prop->getName()] = $prop;
		}
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function isPropertyMapped($name)
	{
	    return isset($this->_mappedProperties[(string) $name]);
	}
	
	/**
	 * @param string $name
	 * @return Atomik_Model_Descriptor_Property
	 */
	public function getMappedProperty($name)
	{
	    if (!isset($this->_mappedProperties[(string) $name])) {
	        return null;
	    }
	    return $this->_mappedProperties[(string) $name];
	}
	
	/**
	 * @return array
	 */
	public function getMappedProperties()
	{
	    return $this->_mappedProperties;
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
		if (!isset($this->_fields[(string) $name])) {
			return null;
		}
		return $this->_fields[(string) $name];
	}
	
	/**
	 * @return array of Atomik_Model_Field
	 */
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * Sets the field which identity the model
	 * 
	 * @param Atomik_Model_Field $field
	 */
	public function setIdentifierField(Atomik_Model_Field $field)
	{
		$this->_identifierField = $field;
	}
	
	/**
	 * @return Atomik_Model_Field
	 */
	public function getIdentifierField()
	{
		return $this->_identifierField;
	}
	
	/**
	 * Sets the field which identity the type of model when
	 * using inheritance
	 * 
	 * @param Atomik_Model_Field $field
	 */
	public function setDescriminatorField(Atomik_Model_Field $field)
	{
		$this->_descriminatorField = $field;
	}
	
	/**
	 * @return Atomik_Model_Field
	 */
	public function getDescriminatorField()
	{
		return $this->_descriminatorField;
	}
	
	/**
	 * Checks if a a field is the source field of an association
	 * 
	 * @param mixed $field
	 * @return bool
	 */
	public function isFieldPartOfAssociation($field)
	{
	    foreach ($this->_associations as $assoc) {
	        if ($assoc->getSourceField() == (string) $field) {
	            return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAssociation($name)
	{
		return isset($this->_associations[(string) $name]);
	}
	
	/**
	 * @param string $name 
	 * @return Atomik_Model_Association
	 */
	public function getAssociation($name)
	{
		if (!isset($this->_associations[(string) $name])) {
			return null;
		}
		return $this->_associations[(string) $name];
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
	 * Checks if their an association between this model and the 
	 * one specified
	 * 
	 * @param Atomik_Model_Descriptor $descriptor
	 * @return bool
	 */
	public function isModelAssociated(Atomik_Model_Descriptor $descriptor)
	{
		return count($this->getAssociations($descriptor)) > 0;
	}
	
	/**
	 * @param Atomik_Model_Behaviour $behaviour
	 */
	public function addBehaviour(Atomik_Model_Behaviour $behaviour)
	{
	    $this->addListener($behaviour);
	    $this->_behaviours[$behaviour->getName()] = $behaviour;
	}
	
	/**
	 * @param string $name
	 */
	public function hasBehaviour($name)
	{
	    return isset($this->_behaviours[$name]);
	}
	
	/**
	 * @param string $name
	 * @return Atomik_Model_Behaviour
	 */
	public function getBehaviour($name)
	{
	    if (!isset($this->_behaviours[$name])) {
	        return null;
	    }
	    return $this->_behaviours[$name];
	}
	
	/**
	 * @return array of Atomik_Model_Behaviour
	 */
	public function getBehaviours()
	{
	    return $this->_behaviours;
	}
	
	/**
	 * Adds an event listener
	 * 
	 * @param Atomik_Model_EventListener $listener
	 */
	public function addListener(Atomik_Model_EventListener $listener)
	{
	    $this->_listeners[] = $listener;
	}
	
	/**
	 * Notify event listeners
	 * 
	 * @param string $event
	 * @param array $args
	 */
	public function notify($event)
	{
	    $args = func_get_args();
	    
	    if ($this->_parent !== null) {
	        call_user_func_array(array($this->_parent, 'notify'), $args);
	    }
	    
	    array_shift($args);
	    array_unshift($args, $this);
	    
		foreach ($this->_listeners as $listener) {
			call_user_func_array(array($listener, $event), $args);
		}
	}
	
	/**
	 * Creates a model instance using data from the database
	 * 
	 * @param array $data
	 * @return Atomik_Model
	 */
	public function hydrate($data)
	{
		$this->notify('BeforeCreateInstance', new ArrayObject($data));
		
		$instance = $this->getHydrator()->hydrate($data);
		
		$this->notify('AfterCreateInstance', $instance);
		
		return $instance;
	}
}
