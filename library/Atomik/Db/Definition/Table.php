<?php

/** Atomik_Db_Definition_Column */
require_once 'Atomik/Db/Definition/Column.php';

/** Atomik_Db_Definition_Index */
require_once 'Atomik/Db/Definition/Index.php';

class Atomik_Db_Definition_Table
{
	public $definition;
	
	public $name;
	
	public $columns = array();
	
	public $indexes = array();
	
	public $primaryKey;
	
	public function __construct(Atomik_Db_Definition $definition, $tableName)
	{
		$this->definition = $definition;
		$this->name = $tableName;
	}
	
	public function column($name, $type, $length = null, $options = array())
	{
		$this->createColumn($name, $type, $length, $options);
		return $this;
	}
	
	public function createColumn($name, $type, $length = null, $options = array())
	{
		$column = new Atomik_Db_Definition_Column($this, $name, $type, $length, $options);
		$this->columns[] = $column;
		return $column;
	}
	
	public function primaryKey($column)
	{
		$this->primaryKey = $column;
		return $this;
	}
	
	public function index($column, $name = null)
	{
		$this->createIndex($column, $name);
		return $this;
	}
	
	public function createIndex($column, $name = null)
	{
		if ($name === null) {
			$name = 'idx_' . $this->name . '_' . $column;
		}
		$index = new Atomik_Db_Definition_Index($this, $name, $column);
		$this->indexes[] = $index;
		return $index;
	}
	
	public function end()
	{
		return $this->definition;
	}
}