<?php

class Atomik_Db_Definition_Index
{
	public $table;
	
	public $name;
	
	public $column;
	
	public function __construct(Atomik_Db_Definition_Table $table, $name, $column)
	{
		$this->table = $table;
		$this->name = $name;
		$this->column = $column;
	}
}