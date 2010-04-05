<?php

class Atomik_Db_Schema_Generator
{
	/**
	 * @var Atomik_Db_Adapter_Interface
	 */
	protected $_adapter;
	
	/**
	 * @var Atomik_Db_Schema
	 */
	protected $_schema;
	
	public function __construct(Atomik_Db_Adapter_Interface $adapter)
	{
		$this->_adapter = $adapter;
	}
	
	public function generate(Atomik_Db_Schema $schema)
	{
		$this->_schema = $schema;
		$sql = '';
		
		if ($schema->dropBeforeCreate) {
			foreach ($schema->tables as $table) {
				$sql .= $this->_buildDrop($table);
			}
		}
		
		foreach ($schema->tables as $table) {
			$sql .= $this->_buildTable($table);
		}
		
		return $sql;
	}
	
	protected function _buildDrop(Atomik_Db_Schema_Table $table)
	{
		return sprintf("DROP TABLE IF EXISTS %s;\n", $this->_adapter->quoteIdentifier($table->name));
	}
	
	protected function _buildTable(Atomik_Db_Schema_Table $table)
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
	
	protected function _buildColumn(Atomik_Db_Schema_Column $column)
	{
		$sql = $this->_adapter->quoteIdentifier($column->name) . ' ' . strtoupper($column->type->getSqlType());
		
		if (isset($column->options['default'])) {
			$sql .= ' DEFAULT ' . $this->_adapter->quote($column->options['default']);
		}
		
		if (isset($column->options['auto-increment']) && $column->options['auto-increment']) {
			$sql .= ' ' . $this->_buildAutoIncrement($column);
		}
		
		return $sql;
	}
	
	protected function _buildAutoIncrement(Atomik_Db_Schema_Column $column)
	{
		return 'AUTO_INCREMENT';
	}
	
	protected function _buildIndex(Atomik_Db_Schema_Index $index)
	{
		return sprintf("CREATE INDEX %s ON %s(%s);\n", 
			$this->_adapter->quoteIdentifier($index->name), 
			$this->_adapter->quoteIdentifier($index->table->name), 
			$this->_adapter->quoteIdentifier($index->column));
	}
}