<?php

abstract class Atomik_Model_Query_Filter_Abstract
{
	protected $_descriptor;
	
	protected $_field;
	
	protected $_value;
	
	public function __construct($descriptor, $field, $value = null)
	{
		$this->setDescriptor($descriptor);
		$this->setField($field);
		$this->setValue($value);
	}
	
	public function setDescriptor($descriptor)
	{
	    $this->_descriptor = Atomik_Model_Descriptor::factory($descriptor);
	}
	
	public function getDescriptor()
	{
		return $this->_descriptor;
	}
	
	public function setField($field)
	{
	    if (!$this->_descriptor->hasField($field)) {
            require_once 'Atomik/Model/Query/Exception.php';
	        throw new Atomik_Model_Query_Exception("Descriptor '" 
	            . $this->_descriptor->getName() . "' has no field '$field'");
	    }
	    
	    if (is_string($field)) {
	        $field = $this->_descriptor->getField($field);
	    }
	    
	    $this->_field = $field;
	}
	
	public function getField()
	{
		return $this->_field;
	}
	
	public function setValue($value)
	{
		$this->_value = $value;
	}
	
	public function getValue()
	{
		return $this->_value;
	}
	
	abstract public function apply(Atomik_Db_Query $query);
	
	protected function _getSqlField()
	{
	    return sprintf('%s.%s', 
	        $this->_descriptor->getTableName(),
	        $this->_field->getColumnName());
	}
}