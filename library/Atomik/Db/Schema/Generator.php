<?php

class Atomik_Db_Schema_Generator
{
	/** @var Atomik_Db_Adapter_Interface */
	protected $_adapter;
	
	/** @var Atomik_Db_Schema */
	protected $_schema;
	
	public function __construct(Atomik_Db_Adapter_Interface $adapter)
	{
		$this->_adapter = $adapter;
	}
	
	public function generateSchema(Atomik_Db_Schema $schema)
	{
		$this->_schema = $schema;
		$sql = '';
		
		foreach ($schema->getTables() as $table) {
			$sql .= $this->generateTable($table);
		}
		
		return $sql;
	}
	
	public function generateDrop(Atomik_Db_Schema_Table $table)
	{
	    return sprintf("DROP TABLE IF EXISTS %s;\n", 
	        $this->_adapter->quoteIdentifier($table->getName()));
	}
	
	public function generateTable(Atomik_Db_Schema_Table $table)
	{
		$columns = array();
		foreach ($table->getColumns() as $column) {
			$columns[] = "\t" . $this->generateColumn($column);
		}
		
		if (($pk = $table->getPrimaryKey()) !== null) {
			$columns[] = "\tPRIMARY KEY (" 
			           . $this->_adapter->quoteIdentifier($pk->getName()) . ')';
		}
		
		$sql = sprintf("CREATE TABLE %s (\n%s\n);\n", 
			$this->_adapter->quoteIdentifier($table->getName()), implode(",\n", $columns));
		
		foreach ($table->getIndexes() as $index) {
			$sql .= $this->generateIndex($table, $index);
		}
		
		return $sql;
	}
	
	public function generateColumn(Atomik_Db_Schema_Column $column)
	{
		$sql = $this->_adapter->quoteIdentifier($column->getName()) 
		     . ' ' . strtoupper($column->getType()->getSqlType());
		
		if (!$column->isNullable()) {
		    $sql .= ' NOT NULL';
		}
		
		if (($default = $column->getDefaultValue()) !== null) {
		    if (!($default instanceof Atomik_Db_Expr)) {
		        $default = $this->_adapter->quote($default);
		    }
			$sql .= ' DEFAULT ' . $default;
		}
		
		$options = $column->getOptions();
		
		if (isset($options['auto-increment']) && $options['auto-increment']) {
			$sql .= ' ' . $this->generateAutoIncrement($column);
		}
		
		return $sql;
	}
	
	public function generateAutoIncrement(Atomik_Db_Schema_Column $column)
	{
		return 'AUTO_INCREMENT';
	}
	
	public function generateIndex(Atomik_Db_Schema_Table $table, Atomik_Db_Schema_Index $index)
	{
		return sprintf("CREATE INDEX %s ON %s(%s);\n", 
			$this->_adapter->quoteIdentifier($index->getName()), 
			$this->_adapter->quoteIdentifier($table->getName()), 
			$this->_adapter->quoteIdentifier($index->getColumn()->getName()));
	}
}