<?php

class Atomik_Db_Schema_Column
{
	public $table;
	
	public $name;
	
	public $type;
	
	public $options = array();
	
	public function __construct(Atomik_Db_Schema_Table $table, $name, Atomik_Db_Type_Abstract $type, $options = array())
	{
		$this->table = $table;
		$this->name = $name;
		$this->type = $type;
		$this->options = $options;
	}
	
	public function index($name = null)
	{
		$this->createIndex($name);
		return $this;
	}
	
	public function createIndex($name = null)
	{
		return $this->table->createIndex($this->name, $name);
	}
}