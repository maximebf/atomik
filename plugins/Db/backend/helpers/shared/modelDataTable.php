<?php

Atomik::loadHelper('dataTable');

class ModelDataTableHelper extends DataTableHelper
{
	protected $_descriptor;
	
	public function modelDataTable($id = null, Atomik_Model_Query $query = null, $options = array())
	{
		$this->dataTable($id, array(), $options);
		
		if ($this->options['sortColumn']) {
			$query->orderBy($this->options['sortColumn'], $this->options['sortOrder']);
		}
		
		$this->options['paginateData'] = false;
		$this->options['sortData'] = false;
		$this->options['numberOfRows'] = Atomik_Model_Locator::count($query);
		
		$offset = ($this->options['currentPage'] - 1) * $this->options['rowsPerPage'];
		$query->limit($offset, $this->options['rowsPerPage']);
		
		$this->_descriptor = $query->getDescriptor();
		$this->setData(Atomik_Model_Locator::query($query));
		
		return $this;
	}
	
	public function renderValue($row, $column)
	{
		if (!$this->_descriptor->hasField($column)) {
			return $row[$column];
		}
		return $this->_descriptor->getField($column)->render($row[$column]);
	}
}