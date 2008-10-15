<?php
/**
 * Atomik Framework
 * Copyright (c) 2008 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/** Atomik_Model_Field_Abstract */
require_once 'Atomik/Model/Field/Abstract.php';

/**
 * A model builder can be used to programatically build models
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder implements ArrayAccess
{
	const HAS_ONE = 'one';
	
	const HAS_PARENT = 'parent';
	
	const HAS_MANY = 'many';
	
	/**
	 * Model class name
	 *
	 * @var string
	 */
	protected $_class;
	
	/**
	 * Model name
	 *
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @var array
	 */
	protected $_adapter;
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var array
	 */
	protected $_references = array();
	
	/**
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * @var array
	 */
	protected $_validationMessages = array();
	
	/**
	 * @var Atomik_Model_Adapter_Interface
	 */
	protected static $_defaultAdapter;
	
	/**
	 * @var array
	 */
	protected static $_builderCache = array();
	
	/**
	 * Sets the default adapter 
	 *
	 * @param Atomik_Model_Adapter_Interface $adapter
	 */
	public static function setDefaultAdapter(Atomik_Model_Adapter_Interface $adapter)
	{
		self::$_defaultAdapter = $adapter;
	}
	
	/**
	 * Returns the default adapter
	 *
	 * @return Atomik_Model_Adapter_Interface
	 */
	public static function getDefaultAdapter()
	{
		if (self::$_defaultAdapter === null) {
			require_once 'Atomik/Model/Adapter/Local.php';
			return new Atomik_Model_Adapter_Local();
		}
		return self::$_defaultAdapter;
	}
	
	/**
	 * Creates a builder from a model class. Created builder are cached.
	 *
	 * @param string|Atomik_Model $class
	 * @return Atomik_Model_Builder
	 */
	public static function createFromClass($class)
	{
		$classname = is_string($class) ? $class : get_class($class);
		
		if (!isset(self::$_builderCache[$classname])) {
			/* creates the builder */
			self::$_builderCache[$classname] = new self($classname);
			self::$_builderCache[$classname]->_class = $classname;
			self::$_builderCache[$classname]->buildFromMetadata(self::getMetadataFromClass($classname));
		}
		
		return self::$_builderCache[$classname];
	}
	
	/**
	 * Clears the cache containing the builder associated with classes
	 */
	public static function invalidateClassBuilderCache()
	{
		self::$_builderCache = array();
	}
	
	/**
	 * Creates a builder from an array of metadata. Can create anonymous builder
	 * if the first argument is an array. Anonymous builder can't have any references.
	 *
	 * @param string|array $spec A string for a model name or an array of metadata
	 * @param array $metadata OPTIONAL An array of metadata if the first argument is a string 
	 * @return Atomik_Model_Builder
	 */
	public static function createFromMetadata($spec, $metadata = null)
	{
		if (is_array($spec)) {
			$metadata = $spec;
			$spec = null;
		}
		
		$builder = new self($spec);
		
		if ($metadata !== null) {
			$builder->buildFromMetadata($metadata);
		}
		
		return $builder;
	}
	
	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param array $metadata OPTIONAL
	 */
	public function __construct($name, $metadata = null)
	{
		$this->_name = $name;
		if ($metadata !== null) {
			$this->buildFromMetadata($metadata);
		}
	}
	
	/**
	 * Returns the model's class name
	 *
	 * @return string
	 */
	public function getClass()
	{
		return $this->_class;
	}
	
	/**
	 * Sets the model name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}
	
	/**
	 * Returns the model name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Sets the adapter
	 *
	 * @param Atomik_Model_Adapter_Interface $adapter
	 */
	public function setAdapter(Atomik_Model_Adapter_Interface $adapter = null)
	{
		if ($adapter === null) {
			$adapter = self::getDefaultAdapter();
		}
		$this->_adapter = $adapter;
	}
	
	/**
	 * Returns the adapter associated to the model
	 *
	 * @return array
	 */
	public function getAdapter()
	{
		if ($this->_adapter === null) {
			$this->setAdapter();
		}
		return $this->_adapter;
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
	 * Adds a new field
	 *
	 * @param Atomik_Model_Field_Abstract $field
	 */
	public function addField(Atomik_Model_Field_Abstract $field)
	{
		$this->_fields[$field->getName()] = $field;
	}
	
	/**
	 * Checks if a field exists
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasField($name)
	{
		return isset($this->_fields[$name]);
	}
	
	/**
	 * Returns params of a field
	 *
	 * @param string $name
	 * @return Atomik_Model_Field_Abstract|bool
	 */
	public function getField($name)
	{
		if (!isset($this->_fields[$name])) {
			return false;
		}
		return $this->_fields[$name];
	}
	
	/**
	 * Gets the field used as the primary key.
	 * The field should have a "@primary-key" tag in its doc-block
	 * Default value is "id".
	 *
	 * @return Atomik_Model_Field_Abstract
	 */
	public function getPrimaryKeyField()
	{
		/* searches for fields with the primary-key option */
		foreach ($this->_fields as $field) {
			if ($field->hasOption('primary-key') && $field->getOption('primary-key')) {
				return $field;
			}
		}
		
		/* no fields found, checks if there is a field named id */
		if (($field = $this->getField('id')) !== false) {
			/* the id field becomes the primary-key */
			$field->setOption('primary-key', true);
			return $field;
		}
		
		/* creates an id field and sets it as the primary key */
		$field = new Atomik_Model_Field_Default('id', array('primary-key' => true, 'form-ignore' => true));
		$this->addField($field);
		return $field;
	}
	
	/**
	 * Returns all fields
	 *
	 * @return array
	 */
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * Resets all the references
	 *
	 * @param array $references
	 */
	public function setReferences($references = array())
	{
		$this->_references = array();
		foreach ($references as $reference) {
			if (is_array($reference)) {
				if (!isset($reference['using'])) {
					$reference['using'] = null;
				}
				$this->addReference($reference['type'], $reference['model'], $reference['using']);
			} else {
				$this->addReferenceFromString($reference);
			}
		}
	}
	
	/**
	 * Adds a new reference
	 * 
	 * References are used to describe relations between models. There is two
	 * types of references: one and many. The first one points to one model and the
	 * second one to more than one. The pointed model is called a foreign model.
	 * 
	 * The property used to access the reference can be set as a model name alias. (see $model)
	 * If not set, the foreign model name will be used.
	 * 
	 * The condition that links two model can be defined using the $using parameter.
	 * It can be an array containing a foreignField key which defined the field to use on the
	 * foreign model and a localField key which defined the field to use on the local model.
	 * The field's value must be equal for a relation to be established.
	 * 
	 * A string can also be used to defined the using statement. It should be of the form
	 * localModel.localField = foreignModel.foreignField
	 *
	 * @param string $type The reference type (one or many)
	 * @param string|array $model The model name. An array can be used to use an alias (ie. the property name) - the alias is the array key.
	 * @param string|array $using OPTIONAL A string or an array with a localField and a foreignField key.
	 */
	public function addReference($type, $model, $using = null)
	{
		$reference = array('type' => $type);
		
		/* gets the property used to access this reference */
		if (is_array($model)) {
			$reference['model'] = current($model);
			$reference['property'] = key($model);
		} else {
			$reference['property'] = $reference['model'] = $model;
		}
		
		if (isset($this->_references[$reference['property']])) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Two references can\'t have the same property');
		}
		
		if ($using !== null) {
			/* the using statement is defined by the model */
			if (is_string($using)) {
				/* using defined as a string, needs parsing */
				/* only the equality operator is supported for the time being */
				if (!preg_match('/(.+)\.(.+)\s(=)\s(.+)\.(.+)/', $using, $matches)) {
					require_once 'Atomik/Model/Exception.php';
					throw new Atomik_Model_Exception('Using statement for reference is malformed: ' . $using);
				}
				
				/* builds the using array */
				if ($matches[1] == $this->_name) {
					$using = array('localField' => $matches[2], 'foreignField' => $matches[5]);
				} else {
					$using = array('localField' => $matches[5], 'foreignField' => $matches[2]);
				}
				$using['operator'] = $matches[3];
				
			} else {
				if (!isset($using['operator'])) {
					$using['operator'] = '=';
				}
			}
			
		} else {
			/* building the using statement */
			/* fetching the builder for the foreign model */
			$refBuilder = self::createFromClass($reference['model']);
			
			if ($type == self::HAS_PARENT) {
				/* creating a using statement for parent references
				 * foreignModel.foreignPrimaryKey = localModel.foreignModel_foreignPrimaryKey */
				$foreignPrimaryKey = $refBuilder->getPrimaryKeyField()->getName();
				$using = array(
					'localField' => strtolower($reference['model']) . '_' . $foreignPrimaryKey,
					'foreignField' => $foreignPrimaryKey,
					'operator' => '='
				);
				
			} else if ($type == self::HAS_ONE) {
				/* creating a using statement for has-one references
				 * foreignModel.localModel_localPrimaryKey = localModel.localPrimaryKey */
				$localPrimaryKey = $this->getPrimaryKeyField()->getName();
				$using = array(
					'localField' => $localPrimaryKey,
					'foreignField' => strtolower($this->_name) . '_' . $localPrimaryKey,
					'operator' => '='
				);
				
			} else {
				/* searching through the foreign model references for one pointing back to this model */
				$parentsRef = $refBuilder->getReferences();
				foreach ($parentsRef as $parentRef) {
					if ($parentRef['type'] == self::HAS_PARENT && $parentRef['model'] == $this->_name) {
						$using = array(
							'localField' => $parentRef['using']['foreignField'],
							'foreignField' => $parentRef['using']['localField'],
							'operator' => $parentRef['using']['operator']
						);
						break;
					}
				}
			}
		}
			
		$reference['using'] = $using;
		
		/* creating the localField if it does not exist */
		if (!isset($this->_fields[$using['localField']])) {
			$this->addField(new Atomik_Model_Field_Default($using['localField'], array('form-ignore' => true)));
		}
		
		$this->_references[$reference['property']] = $reference;
	}
	
	/**
	 * Adds a new reference using a string:
	 * (one|many) foreignModel [as property] [using localModel.localField = foreignModel.foreignField]
	 *
	 * @param string $string
	 */
	public function addReferenceFromString($string)
	{
		$regexp = '/(one|many|parent)\s+(.+)((\sas\s(.+))|)((\susing\s(.+))|)$/U';
		if (!preg_match($regexp, $string, $matches)) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Reference string is malformed: ' . $string);
		}
		
		$type = $matches[1];
		$model = trim($matches[2]);
		$using = null;
		
		/* property name */
		if (isset($matches[5])) {
			$model = array(trim($matches[5]) => $model);
		}
		
		/* using statement */
		if (isset($matches[7])) {
			$using = $matches[8];
		}
		
		$this->addReference($type, $model, $using);
	}
	
	/**
	 * Returns a reference
	 *
	 * @param string $name The reference property name
	 * @return array|bool False if not found
	 */
	public function getReference($name)
	{
		if (!isset($this->_references[$name])) {
			return false;
		}
		return $this->_references[$name];
	}
	
	/**
	 * Returns all references or only the one associated to a model
	 *
	 * @param string $model OPTIONAL A model name
	 * @param string $type OPTIONAL A reference type
	 * @return array
	 */
	public function getReferences($model = null, $type = null)
	{
		if ($model === null) {
			return $this->_references;
		}
		
		$references = array();
		foreach ($this->_references as $reference) {
			if ($reference['model'] == $model) {
				if ($type === null || ($type !== null && $reference['type'] == $type)) {
					$references[] = $reference;
				}
			}
		}
		return $references;
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
	 * Checks if the option exists
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
	 * Creates a model instance
	 *
	 * @see Atomik_Model::__construct()
	 * @param array $values OPTIONAL
	 * @param bool $new OPTIONAL	
	 * @return Atomik_Model
	 */
	public function createInstance($values = array(), $new = true)
	{
		if ($this->_class !== null) {
			$class = $this->_class;
			$instance = new $class($values, $new);
		} else {
			$instance = new Atomik_Model($values, $new);
		}
		$instance->setBuilder($this);
		return $instance;
	}
	
	/**
	 * Validates data against fields validators
	 *
	 * @param array|Atomik_Model $data
	 * @return bool
	 */
	public function isValid($data)
	{
		$isValid = true;
		$this->_validationMessages = array();
		$fields = $this->getFields();
		
		foreach ($fields as $field) {
			if (isset($data[$field->getName()])) {
				if (!$field->isValid($data[$field->getName()])) {
					$this->_validationMessages += $field->getValidationMessages();
					$isValid = false;
				}
			} else if ($field->getOption('required', false)) {
				$this->_validationMessages = 'Missing required field: ' . $field->getName();
				$isValid = false;
			}
		}
		
		return $isValid;
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
	 * Returns a form for this builder
	 *
	 * @return Atomik_Model_Form
	 */
	public function getForm()
	{
		require_once 'Atomik/Model/Form.php';
		return new Atomik_Model_Form($this);
	}
	
	/**
	 * Builds the builder from an array of metadata
	 * 
	 * @param array $metadata OPTIONAL
	 */
	public function buildFromMetadata($metadata)
	{
		/* sets fields */
		if (isset($metadata['fields'])) {
			foreach ($metadata['fields'] as $name => $options) {
				/* gets the field class */
				if (isset($options['field-type'])) {
					$classname = $options['field-type'];
				} else {
					$classname = 'Atomik_Model_Field_Default';
				}
				$field = new $classname($name);
				
				if (isset($options['label'])) {
					$field->setLabel($options['label']);
					unset($options['label']);
				}
				$field->setOptions($options);
				
				$this->addField($field);
			}
			unset($metadata['fields']);
		}
	
		/* sets references */
		if (isset($metadata['references'])) {	
			$this->setReferences($metadata['references']);
			unset($metadata['references']);
		}
		
		/* sets the adapter */
		if (isset($metadata['adapter'])) {
			$classname = $metadata['adapter'];
			$this->setAdapter(new $classname());
			unset($metadata['adapter']);
		} else {
			$this->setAdapter();
		}
		
		/* use the remaining metadatas as options */
		$this->setOptions($metadata);
	}
	
	/**
	 * Builds a metadata array from a model class doc blocks
	 *
	 * @see Atomik_Model_Builder::buildFromMetadata()
	 * @param string $class
	 * @return array
	 */
	public static function getMetadataFromClass($class)
	{
		$class = new ReflectionClass($class);
		$metadata = self::_getMetadataFromDocBlock($class->getDocComment());
				
		$metadata['fields'] = array();
		foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			/* retreives property metadatas */
			$propData = self::_getMetadataFromDocBlock($prop->getDocComment());
			
			/* jump to the next property if there is the ignore tag */
			if (isset($propData['ignore'])) {
				continue;
			}
			
			/* adds the property to the field list */
			$metadata['fields'][$prop->getName()] = $propData;
		}
		
		/* has-one references */
		$metadata['references'] = array();
		if (isset($metadata['has'])) {
			$metadata['references'] = (array) $metadata['has'];
			unset($metadata['has']);
		}
		
		return $metadata;
	}
	
	/**
	 * Retreives metadata tags (i.e. the one starting with @) from a doc block
	 *
	 * @param string $doc
	 * @return array
	 */
	protected static function _getMetadataFromDocBlock($doc)
	{
		$metadata = array();
		preg_match_all('/@(.+)$/mU', $doc, $matches);
		
		for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
			if (($separator = strpos($matches[1][$i], ' ')) !== false) {
				$key = trim(substr($matches[1][$i], 0, $separator));
				$value = trim(substr($matches[1][$i], $separator + 1));
			} else {
				$key = trim($matches[1][$i]);
				$value = true;
			}
			
			if (isset($metadata[$key]) && !is_array($metadata[$key])) {
				$metadata[$key] = array($metadata[$key]);
			}
			
			if (isset($metadata[$key])) {
				$metadata[$key][] = $value;
			} else {
				$metadata[$key] = $value;
			}
		}
		
		return $metadata;
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetExists($index)
	{
		return isset($this->_fields[$index]);
	}
	
	public function offsetGet($index)
	{
		return $this->_fields[$index];
	}
	
	public function offsetSet($index, $value)
	{
		$this->_fields[$index] = $value;
	}
	
	public function offsetUnset($index)
	{
		unset($this->_fields[$index]);
	}
}