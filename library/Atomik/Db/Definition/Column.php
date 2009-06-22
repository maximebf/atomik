<?php

class Atomik_Db_Definition_Column
{
	public $table;
	
	public $name;
	
	public $type;
	
	public $length;
	
	public $options = array();
	
	public function __construct(Atomik_Db_Definition_Table $table, $name, $type, $length = null, $options = array())
	{
		$this->table = $table;
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
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