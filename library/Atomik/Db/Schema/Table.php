<?php

/** Atomik_Db_Schema_Column */
require_once 'Atomik/Db/Schema/Column.php';

/** Atomik_Db_Schema_Index */
require_once 'Atomik/Db/Schema/Index.php';

class Atomik_Db_Schema_Table
{
	public $schema;
	
	public $name;
	
	public $columns = array();
	
	public $indexes = array();
	
	public $primaryKey;
	
	public function __construct(Atomik_Db_Schema $schema, $tableName)
	{
		$this->schema = $schema;
		$this->name = $tableName;
	}
	
	public function column($name, $type, $length = null, $options = array())
	{
		$this->createColumn($name, $type, $length, $options);
		return $this;
	}
	
	public function createColumn($name, $type, $length = null, $options = array())
	{
		$column = new Atomik_Db_Schema_Column($this, $name, $type, $length, $options);
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
		$index = new Atomik_Db_Schema_Index($this, $name, $column);
		$this->indexes[] = $index;
		return $index;
	}
	
	public function end()
	{
		return $this->schema;
	}
}