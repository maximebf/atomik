<?php

class Atomik_Db_Definition_Generator
{
	/**
	 * @var Atomik_Db_Adapter_Interface
	 */
	protected $_adapter;
	
	/**
	 * @var Atomik_Db_Definition
	 */
	protected $_definition;
	
	public function __construct(Atomik_Db_Adapter_Interface $adapter)
	{
		$this->_adapter = $adapter;
	}
	
	public function generate(Atomik_Db_Definition $definition)
	{
		$this->_definition = $definition;
		$sql = '';
		
		if ($definition->dropBeforeCreate) {
			foreach ($definition->tables as $table) {
				$sql .= $this->_buildDrop($table);
			}
		}
		
		foreach ($definition->tables as $table) {
			$sql .= $this->_buildTable($table);
		}
		
		return $sql;
	}
	
	protected function _buildDrop(Atomik_Db_Definition_Table $table)
	{
		return sprintf("DROP TABLE IF EXISTS %s;\n", $this->_adapter->quoteIdentifier($table->name));
	}
	
	protected function _buildTable(Atomik_Db_Definition_Table $table)
	{
		$columns = array();
		foreach ($table->columns as $column) {
			$columns[] = "\t" . $this->_buildColumn($column);
		}
		
		if (!empty($table->primaryKey)) {
			$columns[] = "\tPRIMARY KEY (" . $this->_adapter->quoteIdentifier($table->primaryKey) . ')';
		}
		
		$sql = sprintf("CREATE TABLE %s (\n%s\n);\n", 
			$this->_adapter->quoteIdentifier($table->name), implode(",\n", $columns));
		
		foreach ($table->indexes as $index) {
			$sql .= $this->_buildIndex($index);
		}
		
		return $sql;
	}
	
	protected function _buildColumn(Atomik_Db_Definition_Column $column)
	{
		$sql = $this->_adapter->quoteIdentifier($column->name) . ' ' . strtoupper($column->type);
		if ($column->length !== null) {
			$sql .= '(' . $column->length . ')';
		}
		
		if (isset($column->options['default'])) {
			$sql .= ' DEFAULT ' . $this->_adapter->quote($column->options['default']);
		}
		
		if (isset($column->options['auto-increment']) && $column->options['auto-increment']) {
			$sql .= ' ' . $this->_buildAutoIncrement($column);
		}
		
		return $sql;
	}
	
	protected function _buildAutoIncrement(Atomik_Db_Definition_Column $column)
	{
		return 'AUTO_INCREMENT';
	}
	
	protected function _buildIndex(Atomik_Db_Definition_Index $index)
	{
		return sprintf("CREATE INDEX %s ON %s(%s);\n", 
			$this->_adapter->quoteIdentifier($index->name), 
			$this->_adapter->quoteIdentifier($index->table->name), 
			$this->_adapter->quoteIdentifier($index->column));
	}
}