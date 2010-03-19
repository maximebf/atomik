<?php

require_once 'Atomik/Model/Query/Filter/Abstract.php';

class Atomik_Model_Query_Filter extends Atomik_Model_Query_Filter_Abstract
{
	protected $_operator;
	
	public static function factory($filterName, $descriptor, $field, $value)
	{
	    $operators = array(
	    	'Equal' => '=', 
	    	'NotEqual' => '!=', 
	    	'GreaterThan' => '>', 
	    	'LowerThan' => '<',
	        'GreaterEqThan' => '>=',
	        'LowerEqThan' => '<=');
	    
	    if (isset($operators[$filterName])) {
	        return new Atomik_Model_Query_Filter($descriptor, $field, $value, $operators[$filterName]);
	    }
	    
	    $className = 'Atomik_Model_Query_Filter_' . $filterName;
	    if (class_exists($className) && is_subclass_of($className, 'Atomik_Model_Query_Filter_Abstract')) {
	        return new $className($descriptor, $field, $value);
	    }
	    
        require_once 'Atomik/Model/Query/Exception.php';
	    throw new Atomik_Model_Query_Exception("Query filter '$filter' not found");
	}
	
	public function __construct($descriptor, $field, $value = null, $operator = '=')
	{
		parent::__construct($descriptor, $field, $value);
		$this->_operator = $operator;
	}
	
	public function setOperator($op)
	{
		$this->_operator = $op;
	}
	
	public function getOperator()
	{
		return $this->_operator;
	}
	
	public function apply(Atomik_Db_Query $query)
	{
	    $where = sprintf('%s %s ?', $this->_getSqlField(), $this->_operator);
	    $query->where($where, $this->_value);
	}
}