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

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model implements ArrayAccess
{
	/**
	 * The model builder attached to this model
	 *
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * Whether the model is a new entry
	 *
	 * @var bool
	 */
	protected $_new = true;
	
	/**
	 * References cache
	 *
	 * @var array
	 */
	protected $_references = array();
	
	/**
	 * Query the adapter
	 *
	 * @param string|Atomik_Model_Builder $builder
	 * @param mixed $query
	 * @return array
	 */
	public static function query($builder, $query)
	{
		if (! $builder instanceof Atomik_Model_Builder) {
			$builder = Atomik_Model_Builder::createFromClass($builder);
		}
		
		return $builder->getAdapter()->query($builder, $query);
	}
	
	/**
	 * Finds many models
	 *
	 * @param string|Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return array
	 */
	public static function findAll($builder, $where = null, $orderBy = '', $limit = '')
	{
		if (! $builder instanceof Atomik_Model_Builder) {
			$builder = Atomik_Model_Builder::createFromClass($builder);
		}
		
		return $builder->getAdapter()->findAll($builder, $where, $orderBy, $limit);
	}
	
	/**
	 * Finds one model
	 *
	 * @param string|Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return Atomik_Model
	 */
	public static function find($builder, $where, $orderBy = '', $limit = '')
	{
		if (! $builder instanceof Atomik_Model_Builder) {
			$builder = Atomik_Model_Builder::createFromClass($builder);
		}
		
		return $builder->getAdapter()->find($builder, $where, $orderBy, $limit);
	}
	
	/**
	 * Constructor
	 *
	 * @param array $data OPTIONAL
	 * @param bool $new OPTIONAL
	 */
	public function __construct($data = array(), $new = true)
	{
		$this->_new = $new;
		$this->setData($data);
	}
	
	/**
	 * Sets the builder attached to this model
	 *
	 * @param Atomik_Model_Builder $builder
	 */
	public function setBuilder(Atomik_Model_Builder $builder = null)
	{
		if ($builder === null) {
			$this->_builder = Atomik_Model_Builder::createFromClass($this);
		} else {
			$this->_builder = $builder;
		}
	}
	
	/**
	 * Gets the builder attached to this model
	 *
	 * @return Atomik_Model_Builder
	 */
	public function getBuilder()
	{
		if ($this->_builder === null) {
			$this->setBuilder();
		}
		return $this->_builder;
	}
	
	/**
	 * Checks if the model is new
	 *
	 * @return bool
	 */
	public function isNew()
	{
		return $this->_new;
	}
	
	/**
	 * Sets data
	 *
	 * @param array $data
	 */
	public function setData($data)
	{
		$fields = $this->getBuilder()->getFields();
		foreach ($fields as $field) {
			if (isset($data[$field->getName()])) {
				$this->{$field->getName()} = $data[$field->getName()];
			}
		}
	}
	
	/**
	 * Sets the primary key value
	 *
	 * @param mixed $value
	 */
	public function setPrimaryKey($value)
	{
		$this->{$this->getBuilder()->getPrimaryKeyField()->getName()} = $value;
	}
	
	/**
	 * Returns the primary key value
	 * 
	 * @return mixed
	 */
	public function getPrimaryKey()
	{
		return $this->{$this->getBuilder()->getPrimaryKeyField()->getName()};
	}
	
	/**
	 * Inits a reference property
	 *
	 * @param string $name
	 */
	protected function _initReference($name)
	{
		/* checks if it's already initialized */
		if (isset($this->_references[$name])) {
			return;
		}
		
		$reference = $this->getBuilder()->getReference($name);
		$where = array($reference['using']['foreignField'] => $this->{$reference['using']['localField']});
		
		if ($reference['type'] == Atomik_Model_Builder::HAS_MANY) {
			$models = self::findAll($reference['model'], $where, $reference['orderby'], $reference['limit']);
			$refArray = new Atomik_Model_ReferenceArray($this, $reference, $models);
			$this->_references[$name] = $refArray;
			return;
		}
		
		$this->_references[$name] =  self::find($reference['model'], $where, $reference['orderby'], $reference['limit']);
	}
	
	/**
	 * Get accessor (to handle reference properties)
	 *
	 * @param string $name
	 */
	public function __get($name)
	{
		/* checks if it's an unitialized field */
		if (($field = $this->getBuilder()->getField($name)) !== false) {
			$this->{$name} = $field->getDefaultValue();
			return $this->{$name};
		}
		
		$this->_initReference($name);
		return $this->_references[$name];
	}
	
	/**
	 * Set accessor to handle references
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		/* checks if it's an unitialized field */
		if (($field = $this->getBuilder()->getField($name)) !== false) {
			$this->{$field->getName()} = $value;
			return;
		}
		
		$this->_initReference($name);
		$reference = $this->getBuilder()->getReference($name);
		
		/* has-many */
		if ($reference['type'] == Atomik_Model_Builder::HAS_MANY) {
			$this->_references[$name]->clear($value);
			return;
		}
		
		/* has-one */
		
		if (isset($this->_references[$name])) {
			/* unsets the foreign key of the current foreign model */
			$this->_references[$name]->{$reference['using']['foreignField']} = null;
		}
		
		/* sets the foreign key of the foreign model */
		$value->{$reference['using']['foreignField']} = $this->{$reference['using']['localField']};
		$this->_references[$name] = $value;
	}
	
	/**
	 * Invalidates the reference cache
	 */
	public function invalidateReferenceCache()
	{
		$this->_references = array();
	}
	
	/**
	 * Validates model field values
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->getBuilder()->isValid($this);
	}
	
	/**
	 * Returns the messages generated during the validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->getBuilder()->getValidationMessages();
	}
	
	/**
	 * Saves
	 *
	 * @return bool Success
	 */
	public function save()
	{
		if ($this->getBuilder()->getOption('validate-on-save', false)) {
			if (!$this->isValid()) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('Model failed to validate before saving:<br/>' . 
					implode('<br/>', $this->getValidationMessages()));
			}
		}
		
		if (!$this->getBuilder()->getAdapter()->save($this)) {
			return false;
		}
		
		/* checks if cascade is enabled */
		if ($this->getBuilder()->getOption('cascade-save', false)) {
			foreach ($this->getBuilder()->getReferences() as $reference) {
				$this->_initReference($reference['property']);
				if ($reference['type'] == Atomik_Model_Builder::HAS_ONE) {
					$this->_references[$reference['property']]->save();
				} else {
					$this->_references[$reference['property']]->saveAll();
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Deletes
	 *
	 * @return bool Success
	 */
	public function delete()
	{
		if (!$this->getBuilder()->getAdapter()->delete($this)) {
			return false;
		}
		
		/* checks if cascade is enabled */
		if ($this->getBuilder()->getOption('cascade-delete', false)) {
			foreach ($this->getBuilder()->getReferences() as $reference) {
				$this->_initReference($reference['property']);
				if ($reference['type'] == Atomik_Model_Builder::HAS_ONE) {
					$this->_references[$reference['property']]->delete();
				} else {
					$this->_references[$reference['property']]->deleteAll();
				}
			}
			$this->invalidateReferenceCache();
		}
		
		$this->_new = true;
		return true;
	}
	
	/**
	 * Transforms the model to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		$fields = $this->getBuilder()->getFields();
		
		foreach ($fields as $field) {
			$data[$field->getName()] = $this->{$field->getName()};
		}
		
		return $data;
	}
	
	/**
	 * Returns a form for this model
	 *
	 * @return Atomik_Model_Form
	 */
	public function getForm()
	{
		require_once 'Atomik/Model/Form.php';
		return new Atomik_Model_Form($this);
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetExists($index)
	{
		return isset($this->{$index});
	}
	
	public function offsetGet($index)
	{
		return $this->{$index};
	}
	
	public function offsetSet($index, $value)
	{
		$this->{$index} = $value;
	}
	
	public function offsetUnset($index)
	{
		unset($this->{$index});
	}
}