<?php

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * 
 *
 */
class Atomik_Model
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
	 * Constructor
	 *
	 * @param array $values OPTIONAL
	 * @param bool $new OPTIONAL
	 */
	public function __construct($values = array(), $new = true)
	{
		$this->_new = $new;
		
		foreach ($values as $key => $value) {
			if (is_string($key)) {
				$this->{$key} = $value;
			}
		}
	}
	
	/**
	 * Sets the builder attached to this model
	 *
	 * @param Atomik_Model_Builder $builder
	 */
	public function setBuilder(Atomik_Model_Builder $builder = null)
	{
		if ($builder === null) {
			$this->_builder = new Atomik_Model_Builder($this);
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
	 * Get accessor (to handle reference properties)
	 *
	 * @param string $name
	 */
	public function __get($name)
	{
		$references = $this->getBuilder()->getMetadata('references', array());
		if (!isset($references[$name])) {
			throw new RuntimeException('Property ' . get_class($this) . '::' . 
				$name . ' does not exists');
		}
		$ref = $references[$name];
		
		if ($ref['using'][0]['model'] == get_class($this)) {
			$dest = $ref['using'][1];
			$orig = $ref['using'][0];
		} else {
			$dest = $ref['using'][0];
			$orig = $ref['using'][1];
		}
		
		$where = array($dest['field'] => $this->{$orig['field']});
		
		if ($ref['type'] == 'has-many') {
			return ModelLocator::findAll($dest['model'], $where);
		} else {
			return ModelLocator::find($dest['model'], $where);
		}
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
	 * Saves
	 *
	 * @return bool Success
	 */
	public function save()
	{
		return $this->getBuilder()->getAdapter()->save($this);
	}
	
	/**
	 * Deletes
	 *
	 * @return bool Success
	 */
	public function delete()
	{
		if ($this->getBuilder()->getAdapter()->delete($this)) {
			$this->_new = true;
			return true;
		}
		return false;
	}
	
	/**
	 * Transforms the model to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		$fields = $this->getBuilder()->getMetadata('fields', array());
		foreach ($fields as $field) {
			$data[$field['name']] = $this->{$field['property']};
		}
		return $data;
	}
}