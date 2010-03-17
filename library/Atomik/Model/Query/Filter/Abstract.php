<?php

abstract class Atomik_Model_Query_Filter_Abstract
{
	protected $_descriptor;
	
	protected $_field;
	
	protected $_value;
	
	public function __construct(Atomik_Model_Descriptor $descriptor, $field, $value = null)
	{
		if (is_string($field)) {
			$field = $descriptor->getField($field);
		}
		
		$this->_descriptor = $descriptor;
		$this->_field = $field;
		$this->_value = null;
	}
	
	public function getDescriptor()
	{
		return $this->_descriptor;
	}
	
	public function getField()
	{
		return $this->_field;
	}
	
	public function setValue($value)
	{
		$this->_value = $value;
	}
	
	public function hasValue()
	{
		return !empty($this->_value);
	}
	
	public function getValue()
	{
		return $this->_value;
	}
	
	abstract public function getQueryCondition();
	
	abstract public function getPossibleValues();
}