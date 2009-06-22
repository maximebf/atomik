<?php

/** Atomik_Db_Definition_Table */
require_once 'Atomik/Db/Definition/Table.php';

/** Atomik_Db_Definition_Generator */
require_once 'Atomik/Db/Definition/Generator.php';

class Atomik_Db_Definition
{
	public $tables = array();
	
	public $dropBeforeCreate = false;
	
	protected $_instance;
	
	protected $_generator;
	
	public static function create(Atomik_Db_Instance $instance = null)
	{
		if ($instance === null) {
			$instance = Atomik_Db::getInstance();
		}
		return new self($instance);
	}
	
	public function __construct(Atomik_Db_Instance $instance)
	{
		$this->_instance = $instance;
		$this->_generator = $instance->getAdapter()->getDefinitionGenerator();
	}
	
	public function getInstance()
	{
		return $this->_instance;
	}
	
	public function getGenerator()
	{
		return $this->_generator;
	}
	
	public function dropBeforeCreate()
	{
		$this->dropBeforeCreate = true;
		return $this;
	}
	
	public function table($name)
	{
		$table = new Atomik_Db_Definition_Table($this, $name);
		$this->tables[] = $table;
		return $table;
	}
	
	public function toSql()
	{
		return $this->_generator->generate($this);
	}
	
	public function execute()
	{
		
	}
}