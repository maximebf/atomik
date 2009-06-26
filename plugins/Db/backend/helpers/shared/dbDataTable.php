<?php

Atomik::loadHelper('dataTable');

class DbDataTableHelper extends DataTableHelper
{
	public function dbDataTable($id = null, Atomik_Db_Query $query = null, $options = array())
	{
		$this->dataTable($id, array(), $options);
		
		if ($this->options['sortColumn']) {
			$query->orderBy($this->options['sortColumn'], $this->options['sortOrder']);
		}
		
		$countQuery = clone $query;
		$result = $countQuery->count()->execute();
		$numberOfRows = $result->fetchColumn();
		$result->closeCursor();
		
		$this->options['paginateData'] = false;
		$this->options['sortData'] = false;
		$this->options['numberOfRows'] = $numberOfRows;
		
		$offset = ($this->options['currentPage'] - 1) * $this->options['rowsPerPage'];
		$query->limit($offset, $this->options['rowsPerPage']);
		
		$this->setData($query->execute());
		
		return $this;
	}
}