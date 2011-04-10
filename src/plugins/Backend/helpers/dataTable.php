<?php

class DataTableHelper
{
	/**
	 * @var array
	 */
	public static $defaultOptions = array(
		'currentPage'	=> 1,
		'rowsPerPage'	=> 20,
		'numberOfPages'	=> 1,
		'paginate'		=> true,
		'paginateData'	=> true,
		'sortable'		=> true,
		'serverSort'	=> false,
		'sortColumn'	=> false,
		'sortOrder'		=> 'asc',
		'sortData'		=> true
	);
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var array
	 */
	public $options;
	
	/**
	 * @var array
	 */
	public $columns = array();
	
	/**
	 * @var int
	 */
	public $startingRow = 0;
	
	/**
	 * @var int
	 */
	public $maxRow = 20;
	
	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->options = self::$defaultOptions;
		Atomik_Assets::getInstance()->addNamedAsset('dataTable');
	}
	
	/**
	 * Method used for the view helper
	 * 
	 * @param 	string	$id
	 * @param 	array	$data
	 * @param 	array	$options
	 * @return 	DataTableHelper
	 */
	public function dataTable($id = null, $data = array(), $options = array())
	{
		if ($id === null) {
			return $this;
		}
		
		$this->id = $id;
		$this->options = array_merge(self::$defaultOptions, $options);
		
		// options
		$this->options['remote'] = Atomik::url(null, array('dataTableRemote' => $id));
		$this->options['url'] = Atomik::get('url', Atomik::url('edit'), $options);
		
		$this->_setupSorting();
		$this->setData($data);
		
		return $this;
	}
	
	/**
	 * Sets the data
	 * 
	 * @param 	array	$data
	 */
	public function setData($data)
	{
		$this->_data = $data;
		$this->_setupColumns();
		$this->_setupPagination();
		
		if ($this->options['sortData'] && $this->options['sortColumn'] !== false) {
			usort($this->_data, array($this, '_sortData'));
		}
	}
	
	/**
	 * Returns the data
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Returns the data which needs to be displayed
	 * 
	 * @return array
	 */
	public function getDataToDisplay()
	{
		$data = array();
		for($currentRow = $this->startingRow; $currentRow < $this->maxRow; $currentRow++) {
			$data[] = $this->_data[$currentRow];
		}
		return $data;
	}
	
	/**
	 * Sorts the data
	 */
	protected function _sortData($a, $b)
	{
		$column = $this->options['sortColumn'];
		$cmp = strnatcmp($a[$column], $b[$column]);
		
		if ($this->options['sortOrder'] == 'desc') {
			return -$cmp;
		}
		return $cmp;
	}
	
	/**
	 * Setups the table columns
	 */
	protected function _setupColumns()
	{
		$this->columns = array();
		
		if (!isset($this->options['columns'])) {
			// find columns through all rows
			foreach ($this->_data as $row) {
				foreach ($row as $key => $value) {
					if (!isset($this->columns[$key])) {
						$this->columns[$key] = $key;
					}
				}
			}
			
		} else {
			// clean the columns array
			foreach ((array) $this->options['columns'] as $key => $value) {
				if (is_int($key)) {
					$key = $value;
				}
				$this->columns[$key] = $value;
			}
		}
	}
	
	/**
	 * Setups the pagination
	 */
	protected function _setupPagination()
	{
		$dataCount = count($this->_data);
		
											
		$this->options['currentPage'] = Atomik::get('request/dataTablePage', Atomik::get('currentPage', 1, $this->options));
		$this->options['numberOfRows'] = Atomik::get('numberOfRows', $dataCount, $this->options);
		
		$this->options['rowsPerPage'] = Atomik::get('request/dataTableLength', 
											Atomik::get('rowsPerPage', self::$defaultOptions['rowsPerPage'], $this->options));
		if ($this->options['rowsPerPage'] == -1) {
			$this->options['rowsPerPage'] = $this->options['numberOfRows'];
		}
		
		if ($this->options['numberOfRows'] > 0) {
			$this->options['numberOfPages'] = ceil($this->options['numberOfRows'] / $this->options['rowsPerPage']);
		} else {
			$this->options['numberOfPages'] = 1;
		}
		
		if (!$this->options['paginateData']) {
			$this->startingRow = 0;
			$this->maxRow = $dataCount;
		} else {
			$this->startingRow = ($this->options['currentPage'] - 1) * $this->options['rowsPerPage'];
			$this->maxRow = min($this->startingRow + $this->options['rowsPerPage'], $dataCount);
		}
	}
	
	/**
	 * Setups sorting
	 */
	protected function _setupSorting()
	{
		if (!$this->options['sortable']) {
			return;
		}
		
		$this->options['sortColumn'] = Atomik::get('request/dataTableSortColumn', 
											Atomik::get('sortColumn', false, $this->options));
		$this->options['sortOrder'] = Atomik::get('request/dataTableSortOrder', 
											Atomik::get('sortOrder', 'asc', $this->options));
	}
	
	/**
	 * Starts output bufferization to allow generation of a custom tbody.
	 * Do not echo the tbody tag.
	 * 
	 * @return DataTableHelper
	 */
	public function start()
	{
		ob_start();
		return $this;
	}
	
	/**
	 * Stops the output bufferization and renders the full table
	 * 
	 * @return string
	 */
	public function end()
	{
		return $this->renderWrapper('<tbody>' . ob_get_clean() . '</tbody>');
	}
	
	/**
	 * Renders the table without a custom tbodu
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->renderWrapper($this->renderBody());
	}
	
	/**
	 * Renders the wrapper div
	 * 
	 * @param 	string	$tbody
	 * @return 	string
	 */
	public function renderWrapper($tbody)
	{
		// remote call to only get the data
		if (isset($_GET['dataTableRemote']) && $_GET['dataTableRemote'] == $this->id) {
			ob_clean();
			echo $tbody;
			Atomik::end(true);
		}
		
		$table = sprintf("<table id=\"%s\" class=\"dataTable\">\n%s\n%s\n</table>\n%s", 
			$this->id, $this->renderHead(), $tbody, $this->renderScript());
			
		return sprintf("<div id=\"%sWrapper\" class=\"dataTableWrapper\">\n%s\n<div class=\"clear\"></div>\n</div>",
			$this->id, $this->renderSearch() . $table . $this->renderPager());
	}
	
	/**
	 * Renders the thead part of the table
	 * 
	 * @return string
	 */
	public function renderHead()
	{
		$sortClass = $this->options['sortOrder'] == 'asc' ? 'sortAsc' : 'sortDesc';
		
		$thead = "<thead>\n\t<tr>\n";
		foreach ($this->columns as $key => $label) {
			$thead .= sprintf("\t\t<th id=\"%s\" class=\"sortable %s\">%s</th>\n",
				$key,
				$this->options['sortColumn'] == $key ? $sortClass : '',
				ucfirst($label)
			);
		}
		if (count(Atomik::get('actions', array(), $this->options))) {
			$thead .= "\t\t<th class=\"actions\"></th>\n";
		}
		$thead .= "\t</tr>\n</thead>";
		
		return $thead;
	}
	
	/**
	 * Renders the tbody part of the table
	 * 
	 * @return string
	 */
	public function renderBody()
	{
		$clickableColumns = Atomik::get('clickableCols', null, $this->options);
		$idColumn = Atomik::get('idColumn', 'id', $this->options);
		$actions = Atomik::get('actions', array(), $this->options);
		$classes = Atomik::get('classes', array(), $this->options);
		
		$actionsHtml = '';
		if (count($actions)) {
			$actionsHtml = '<td class="actions">';
			foreach ($actions as $action) {
				$actionsHtml .= sprintf('<a href="%s" class="action">%s</a> ', $action['url'], $action['label']);
			}
			$actionsHtml .= '</td>';
		}
		
		$tbody = '<tbody>';
		foreach ($this->getDataToDisplay() as $row) {
			$rel = isset($row[$idColumn]) ? $row[$idColumn] : '';
			$tr = '';
			$rowClasses = '';
			foreach ($this->columns as $key => $value) {
				$colClasses = '';
				$value = '';
				if (isset($row[$key])) {
					$value = (string) $this->renderValue($row, $key);
				}
				if ($clickableColumns !== false || (is_array($clickableColumns) && in_array($key, $clickableColumns))) {
					$colClasses = 'clickable';
				}
				if (isset($classes[$key]) && isset($classes[$key][$value])) {
					$rowClasses = ' ' . $classes[$key][$value];
				}
				$tr .= sprintf("\t\t<td class=\"%s\">%s</td>\n", $colClasses, $value);
			}
			$tbody .=  sprintf("\t<tr class=\"%s\" rel=\"%s\">%s\n%s\t</tr>\n", $rowClasses, $rel, $tr, $actionsHtml);
		}
		
		$tbody .= '</tbody>';
		return $tbody;
	}
	
	/**
	 * Returns a string representing the value
	 * 
	 * @param 	array 	$row
	 * @param 	string	$column
	 * @return 	string
	 */
	public function renderValue($row, $column)
	{
		return $row[$column];
	}
	
	/**
	 * Renders the needed javascript
	 * 
	 * @return string
	 */
	public function renderScript()
	{
		return sprintf('<script type="text/javascript">jQuery(function($) { $("#%sWrapper").dataTable(%s); })</script>', 
			$this->id, json_encode($this->options));
	}
	
	/**
	 * Renders the search box
	 * 
	 * @return string
	 */
	public function renderSearch()
	{
		
	}
	
	/**
	 * Renders the pager
	 * 
	 * @return string
	 */
	public function renderPager()
	{
		$selectLength = '';
		/*$selectLength = '<div class="dataTableLengthWrapper"><label>' . __('Rows per page') . ': </label>'
					  . '<select class="dataTableLength"><option value="10">10</option><option value="2">2</option>'
					  . '<option value="20">20</option><option value="30">30</option>'
					  . '<option value="50">50</option><option value="100">100</option>'
					  . '<option value="-1">' . __('All') . '</option></select></div>';*/
			  
		if (!$this->options['paginate'] || (($numberOfPages = $this->options['numberOfPages']) <= 1)) {
			return $selectLength;
		}
		
		$button = "<li><a href=\"#\" class=\"%s\">%s</a></li>\n";
		$html = "<ul class=\"dataTablePager\">\n" . sprintf($button, 'previous', 'Previous');
		
		for ($i = 1; $i <= min($numberOfPages, 10); $i++) {
			$html .= sprintf("<li><a href=\"#%s\" class=\"page%s\">%s</a></li>\n",
				$i, $this->options['currentPage'] == $i ? ' current' : '', $i);
		}
		
		$html .= sprintf($button, 'next', 'Next') . "</ul>\n" . $selectLength;
		return $html;
	}
	
	/**
	 * @see DataTableHelper::render()
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}
}
