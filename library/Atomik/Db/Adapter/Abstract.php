<?php

/** Atomik_Db_Adapter_Interface */
require_once 'Atomik/Db/Adapter/Interface.php';

abstract class Atomik_Db_Adapter_Abstract implements Atomik_Db_Adapter_Interface
{
	protected $_pdo;
	
	public function __construct(PDO $pdo)
	{
		$this->_pdo = $pdo;
	}
	
	public function getQueryGenerator()
	{
		return new Atomik_Db_Query_Generator($this);
	}
	
	public function getDefinitionGenerator()
	{
		return new Atomik_Db_Definition_Generator($this);
	}
	
	public function quote($value)
	{
		if ($value === null) {
			return 'NULL';
		}
		return $this->_pdo->quote($value);
	}
	
	public function quoteIdentifier($identifier)
	{
		return $identifier;
	}
}