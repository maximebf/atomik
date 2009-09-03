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

/** Atomik_Options */
require_once 'Atomik/Options.php';

/** Atomik_Model_Manager */
require_once 'Atomik/Model/Manager.php';

/** Atomik_Model_Field_Abstract */
require_once 'Atomik/Model/Field/Abstract.php';

/** Atomik_Model_Builder_Reference */
require_once 'Atomik/Model/Builder/Reference.php';

/** Atomik_Model_Builder_Link */
require_once 'Atomik/Model/Builder/Link.php';

/** Atomik_Model_Behaviour_Broker */
require_once 'Atomik/Model/Behaviour/Broker.php';

/**
 * A model builder can be used to programatically build models
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder extends Atomik_Options
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $className;
	
	/**
	 * @var string
	 */
	public $tableName;
	
	/**
	 * @var Atomik_Model_Manager
	 */
	protected $_manager;
	
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_parentModelBuilder;
	
	/**
	 * @var string
	 */
	protected $_inheritanceType;
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var bool
	 */
	protected $_autoPrimaryKey = true;
	
	/**
	 * @var Atomik_Model_Field_Abstract
	 */
	protected $_primaryKeyField;
	
	/**
	 * @var array
	 */
	protected $_references = array();
	
	/**
	 * @var Atomik_Model_Behaviour_Broker
	 */
	protected $_behaviourBroker = array();
	
	/**
	 * @var array
	 */
	protected $_links = array();
	
	/**
	 * Constructor
	 *
	 * @param 	string 	$name
	 * @param 	array 	$metadata
	 */
	public function __construct($name, $className = null, $tableName = null)
	{
		$this->name = $name;
		$this->className = $className;
		$this->tableName = $tableName === null ? $name : $tableName;
		$this->_behaviourBroker = new Atomik_Model_Behaviour_Broker($this);
		$this->setPrimaryKeyField();
	}
	
	/**
	 * Sets the manager associated to this builder
	 * 
	 * @param Atomik_Model_Manager $manager
	 */
	public function setManager(Atomik_Model_Manager $manager = null)
	{
		if ($manager === null) {
			$manager = Atomik_Model_Manager::getDefault();
		}
		$this->_manager = $manager;
	}
	
	/**
	 * Returns the associated model manager
	 * 
	 * @return Atomik_Model_Manager
	 */
	public function getManager()
	{
		if ($this->_manager === null) {
			$this->setManager();
		}
		return $this->_manager;
	}
	
	/**
	 * Sets the parent model
	 * 
	 * @param	string|Atomik_Model_Builder	$parentModel
	 */
	public function setParentModel($parentModel)
	{
		$parent = Atomik_Model_Builder_Factory::get($parentModel);
		$type = $parent->getOption('inheritance', 'abstract');
		
		switch($type) {
			case 'none':
				return;
				
			case 'abstract':
				$this->_fields = array_merge($parent->getFields(), $this->_fields);
				$this->_options = array_merge($parent->getOptions(), $this->_options);
				foreach ($parent->getBehaviourBroker()->getBehaviours() as $behaviour) {
					$this->_behaviourBroker->addBehaviour(clone $behaviour);
				}
				foreach ($parent->getReferences() as $ref) {
					$this->addReference(clone $ref);
				}
				foreach ($parent->getLinks() as $link) {
					$this->addLink(clone $link);
				}
				break;
				
			case 'reference':
				$ref = new Atomik_Model_Builder_Reference('parent', Atomik_Model_Builder_Reference::HAS_PARENT);
				$ref->target = $parent;
				$ref->targetField = $parent->getPrimaryKeyField()->name;
				$ref->sourceField = $ref->target . '_' . $ref->targetField;
				$this->addReference($ref);
				break;
		}
		
		$this->_parentModelBuilder = $parent;
		$this->_inheritanceType = $type;
	}
	
	/**
	 * Checks if it as a parent
	 * 
	 * @return bool
	 */
	public function hasParentModel()
	{
		return $this->_parentModelBuilder !== null;
	}
	
	/**
	 * Returns the parent model builder or null
	 * 
	 * @return Atomik_Model_Builder
	 */
	public function getParentModel()
	{
		return $this->_parentModelBuilder;
	}
	
	/**
	 * Returns the type of inheritance used
	 * 
	 * @return string
	 */
	public function getInheritanceType()
	{
		return $this->_inheritanceType;
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
		$this->_fields[$field->name] = $field;
		
		if ($field->getOption('primary-key', false)) {
			$this->setPrimaryKeyField($field);
		}
	}
	
	/**
	 * Checks if a field exists
	 *
	 * @param 	string $name
	 * @return 	bool
	 */
	public function hasField($name)
	{
		return isset($this->_fields[(string) $name]);
	}
	
	/**
	 * Returns a field object
	 *
	 * @param 	string 	$name
	 * @return 	Atomik_Model_Field_Abstract|bool	False if the field does not exist
	 */
	public function getField($name)
	{
		if (!isset($this->_fields[$name])) {
			return false;
		}
		return $this->_fields[$name];
	}
	
	/**
	 * Returns the field with the specifiec option
	 * 
	 * @param	string	 $option
	 * @return 	Atomik_Model_Field_Abstract
	 */
	public function getFieldWithOption($option)
	{
		foreach ($this->_fields as $field) {
			if ($field->hasOption($option)) {
				return $field;
			}
		}
		return null;
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
	 * Sets the primary key field
	 * 
	 * If $field is null, will use or create a field named id
	 * 
	 * @param Atomik_Model_Field_Abstract $field
	 */
	public function setPrimaryKeyField(Atomik_Model_Field_Abstract $field = null)
	{
		$removeAutoKey = true;
		
		if ($field === null) {
			if ($this->_primaryKeyField !== null) {
				// primary key already defined
				return;
			}
			
			// checks if there is a field named id
			if (($field = $this->getField('id')) === false) {
				require_once 'Atomik/Model/Field.php';
				$field = new Atomik_Model_Field('id', 'int', array('form-ignore' => true));
				$this->addField($field);
				$this->_autoPrimaryKey = true;
				$removeAutoKey = false;
			}
		}
		
		if ($removeAutoKey && $this->_autoPrimaryKey) {
			unset($this->_fields['id']);
			$this->_autoPrimaryKey = false;
		}
		
		$this->_primaryKeyField = $field;
	}
	
	/**
	 * Returns the field used as the primary key.
	 *
	 * @return Atomik_Model_Field_Abstract
	 */
	public function getPrimaryKeyField()
	{
		if ($this->_primaryKeyField === null) {
			$this->setPrimaryKeyField();
		}
		return $this->_primaryKeyField;
	}
	
	/**
	 * Checks if a field is the primary key
	 * 
	 * @param Atomik_Model_Field_Abstract $field
	 * @return bool
	 */
	public function isFieldThePrimaryKey(Atomik_Model_Field_Abstract $field)
	{
		return $this->_primaryKeyField == $field;
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
			$this->addReference($reference);
		}
	}
	
	/**
	 * Adds a new reference
	 * 
	 * @param	Atomik_Model_Builder_Reference	$reference
	 */
	public function addReference(Atomik_Model_Builder_Reference $reference)
	{
		if (!$this->hasField($reference->sourceField)) {
			$this->addField(new Atomik_Model_Field($reference->sourceField, 'int'));
		}
		$this->_references[$reference->name] = $reference;
	}
	
	/**
	 * Checks if a reference exists
	 * 
	 * @param	string	$name
	 * @return 	bool
	 */
	public function hasReference($name)
	{
		return isset($this->_references[$name]);
	}
	
	/**
	 * Returns a reference object
	 *
	 * @param 	string 		$name 
	 * @return 	Atomik_Model_Builder_Reference|bool 	False if not found
	 */
	public function getReference($name)
	{
		if (!isset($this->_references[$name])) {
			return false;
		}
		return $this->_references[$name];
	}
	
	/**
	 * Returns a reference from the source field
	 * 
	 * @param 	Atomik_Model_Field_Abstract 		$field
	 * @return 	Atomik_Model_Builder_Reference
	 */
	public function getReferenceFromSourceField(Atomik_Model_Field_Abstract $field)
	{
		foreach ($this->_references as $reference) {
			if ($reference->sourceField == $field->name) {
				return $reference;
			}
		}
	}
	
	/**
	 * Returns all references or only the one associated to a model
	 *
	 * @param 	string 	$modelName
	 * @param 	string 	$type 		Reference type
	 * @return 	array
	 */
	public function getReferences($targetModel = null, $type = null)
	{
		if ($targetModel === null) {
			return $this->_references;
		}
		
		$references = array();
		foreach ($this->_references as $reference) {
			if ($reference->isTarget($targetModel) && ($type === null || $reference->type == $type)) {
				$references[] = $reference;
			}
		}
		return $references;
	}
	
	/**
	 * Checks if a field is part of a reference
	 * 
	 * @param Atomik_Model_Field_Abstract $field
	 * @return bool
	 */
	public function isFieldPartOfReference(Atomik_Model_Field_Abstract $field)
	{
		foreach ($this->_references as $reference) {
			if ($reference->sourceField == $field->name) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks if the specified model is related to this one
	 * 
	 * @param Atomik_Model_Builder $builder
	 * @return bool
	 */
	public function isModelRelated(Atomik_Model_Builder $builder)
	{
		foreach ($this->_references as $reference) {
			if ($reference->isTarget($builder)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks if the specified model is a child of this one
	 * 
	 * @param Atomik_Model_Builder $builder
	 * @return bool
	 */
	public function isChildModel(Atomik_Model_Builder $builder)
	{
		foreach ($this->_references as $reference) {
			if ($reference->isTarget($builder) && $reference->type != Atomik_Model_Builder_Reference::HAS_PARENT) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks if the specified model is the parent of this one
	 * 
	 * @param Atomik_Model_Builder $builder
	 * @return bool
	 */
	public function isParentModel(Atomik_Model_Builder $builder)
	{
		foreach ($this->_references as $reference) {
			if ($reference->isTarget($builder) && $reference->type == Atomik_Model_Builder_Reference::HAS_PARENT) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the behaviour broker
	 * 
	 * @return Atomik_Model_Behaviour_Broker
	 */
	public function getBehaviourBroker()
	{
		return $this->_behaviourBroker;
	}
	
	/**
	 * Resets all links
	 * 
	 * @param array $links
	 */
	public function setLinks($links)
	{
		$this->_links = array();
		foreach ($links as $link) {
			$this->addLink($link);
		}
	}
	
	/**
	 * Adds a new link
	 * 
	 * @param Atomik_Model_Builder_Link $link
	 */
	public function addLink(Atomik_Model_Builder_Link $link)
	{
		$this->_links[$link->name] = $link;
	}
	
	/**
	 * Checks if this builder as the specified link
	 * 
	 * @param string $name
	 */
	public function hasLink($name)
	{
		return isset($this->_links[$name]);
	}
	
	/**
	 * Returns the link with the specified name
	 * 
	 * @param string $name
	 */
	public function getLink($name)
	{
		if (!isset($this->_links[$name])) {
			return null;
		}
		return $this->_links[$name];
	}
	
	/**
	 * Returns all links
	 * 
	 * @return array
	 */
	public function getLinks()
	{
		return $this->_links;
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
		$className = $this->className;
		if ($className === null) {
			/** Atomik_Model */
			require_once 'Atomik/Model.php';
			$className = 'Atomik_Model';
		}
		
		$dataToFilter = array_intersect_key($values, $this->_fields);
		$data = array_diff_key($values, $this->_fields);
		foreach ($dataToFilter as $key => $value) {
			$data[$key] = $this->_fields[$key]->filterInput($value);
		}
		
		$this->_behaviourBroker->notifyBeforeCreateInstance($this, $data, $new);
		$instance = new $className($data, $new, $this);
		$this->_behaviourBroker->notifyAfterCreateInstance($this, $instance);
		
		return $instance;
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
	 * Returns the builder name
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}