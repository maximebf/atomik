<?php
	/**
	 * DB
	 *
	 * @version 2.0
	 * @package Atomik
	 * @subpackage Db
	 * @author Maxime Bouroumeau-Fuseau
	 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
	 * @license http://www.opensource.org/licenses/mit-license.php
	 * @link http://pimpmycode.fr/atomik
	 */
	 
	/* default configuration */
	config_set_default(array(
	
		/* enable/disable auto connection */
		'database'					=> false,
		
		/* arguments for the database_func_connect function */
		'database_args' 			=> array('localhost', 'root', ''),
		
		/* database schema to use */
		'database_schema' 			=> 'schema',
		
		/* adapter, set to false to directly define functions */
		'database_adapter'			=> 'mysql',
		
		/* predefined adapters */
		'database_adapters' => array(
			'mysql' => array(
				'mysql_connect',
				'mysql_close',
				'mysql_select_db',
				'mysql_query',
				'mysql_fetch_array',
				'mysql_real_escape_string'
			)
		)
	));

	/**
	 * Connects to the database
	 *
	 * @param array $args OPTIONAL Arguments passed to the database_func_connect function
	 * @return mixed Value returned by the database_connect function
	 */
	function db_connect($args = array())
	{
		if (count($args) === 0) {
			/* retreives database_func_connect args from the config */
			$args = config_get('database_args', array());
		}
		
		/* loads an adapter */
		if (($adapter = config_get('database_adapter', 'mysql')) !== false) {
			$adapters = config_get('database_adapters');
			
			/* checks if the adapter exists */
			if (!isset($adapters[$adapter])) {
				trigger_error('Database adapter ' . $adapter . ' not found', E_USER_ERROR);
				return;
			}
			
			/* merge adapter functions inside the config */
			config_merge(array(
				'database_func_connect' 	=> $adapters[$adapter][0],
				'database_func_close' 		=> $adapters[$adapter][1],
				'database_func_selectdb' 	=> $adapters[$adapter][2],
				'database_func_query' 		=> $adapters[$adapter][3],
				'database_func_fetch' 		=> $adapters[$adapter][4],
				'database_func_escape' 		=> $adapters[$adapter][5]
			));
		}
	
		/* calls the connection functions */
		if (!($db = call_user_func_array(config_get('database_func_connect'), $args))) {
			trigger_error('Database connection failed');
		}
		/* stores the result into the config */
		config_set('db', $db);
		
		/* checks if automatic schema selection is enables */
		if (($schema = config_get('database_schema', '')) != '' && 
			($func = config_get('database_func_selectdb')) != '') {
			return call_user_func($func, $schema, $db);
		}
		
		return $db;
	}
	
	/* automatic connection */
	if (config_get('database', false) === true) {
		events_register('core_before_dispatch', 'db_connect');
	}

	/**
	 * Closes the database connection
	 *
	 * @return mixed
	 */
	function db_close()
	{
		return call_user_func(config_get('database_func_close'), config_get('db'));
	}

	/**
	 * Query the database
	 * 
	 * @param string $query
	 * @return mixed
	 */
	function db_query($query)
	{
		return call_user_func(config_get('database_func_query'), $query, config_get('db'));
	}
	
	/**
	 * Selects one rows
	 *
	 * @see db_build_select_sql()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return array
	 */
	function db_select($tables, $where = null, $orderBy = '', $limit = '', $escape = true)
	{
		$sql = db_build_select_sql($tables, $where, $orderBy, $limit, $escape);
		return db_select_sql($sql, true);
	}
	
	/**
	 * Selects multiple rows
	 *
	 * @see db_build_select_sql()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return array
	 */
	function db_select_all($tables, $where = array(), $orderBy = '', $limit = '', $escape = true)
	{
		$sql = db_build_select_sql($tables, $where, $orderBy, $limit, $escape);
		return db_select_sql($sql);
	}

	/**
	 * Returns all rows matching the query
	 *
	 * @param string $query
	 * @param bool $unique OPTIONAL Only one row (default false)
	 * @return array|bool False if no row where found
	 */
	function db_select_sql($query, $unique = false)
	{
		if ($results = db_query($query)) {
			return db_fetch_results($results, $unique);
		}
		
		return false;
	}

	/**
	 * Inserts data into the database
	 * The data array keys are fields name and their
	 * associated values the data. 
	 *
	 * @see db_build_where()
	 * @param string $table
	 * @param array $data
	 * @param bool $escape OPTIONAL (default true) Escape values
	 * @return mixed
	 */
	function db_insert($table, $data, $escape = true)
	{
		$fields = array();
		$values = array();
		
		/* adds column names inside the $cols array and the values inside the $values array */
		foreach ($data as $field => $value) {
			$fields[] = $field;
			
			/* escapes the value */
			if (!is_array($value) && $escape) {
				$value = call_user_func(config_get('database_func_escape'), $value);
			}
			
			/* quotes the values */
			if (!is_array($value)) {
				$value = "'$value'";
			} else {
				$value = $value[0];
			}
			
			$values[] = $value;
		}

		/* builds the sql string */
		$query = 'INSERT INTO ' . $table . '(' . implode(', ', $fields) 
		       . ') VALUES(' . implode(', ', $values) . ')';
		
		/* executes the query */
		return db_query($query);
	}
	
	/**
	 * Deletes rows
	 *
	 * @see db_build_where
	 * @param string $table
	 * @param array $where
	 * @param bool $escape OPTIONAL (default true)
	 * @return bool
	 */
	function db_delete($table, $where, $escape = true)
	{
		$sql = 'DELETE FROM ' . $table . ' WHERE ' . db_build_where($where, $table, $escape);
		return db_query($sql);
	}
	
	/**
	 * Buils an sql string using $tables and $where
	 * Possible situations:
	 *
	 *  - $tables is an array ($where = null):
	 *    Allow to select data from multiple tables
	 *    keys will be treated as tables name. values
	 *    must be an array like $where (see below).
	 *    Example:
	 *     db_build_select_sql(array('table' => array('field1' => 'value1')));
	 *     SELECT * FROM table WHERE table.field1 = 'value1'
	 *
	 *  - $tables is a string ($where is needed):
	 *    Select data from one table. The $where array defines
	 *    conditions. The key is a field name and the value is
	 *    a value. Example:
	 *     db_build_select_sql('table', array('field1' => 'value1'));
	 *     SELECT * FROM table WHERE table.field1 = 'value1'
	 *    
	 *
	 * @see db_build_where()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return array
	 */
	function db_build_select_sql($tables, $where = null, $orderBy = '', $limit = '', $escape = true)
	{
		/* if table is a string, transform it to an array */
		if (!is_array($tables)) {
			/* $where has not been set */
			if ($where === null) {
				trigger_error('The second argument is missing', E_USER_WARNING);
				$where = array();
			}
			$tables = array($tables => $where);
		}
		
		/* creates the sql condition for each key/value pair */
		$conditions = array();
		foreach ($tables as $table => $fields) {
			$condition = db_build_where($fields, $table, $escape);
			if (!empty($condition)) {
				$conditions[] = $condition;
			}
		}
		
		/* ORDER BY */
		if (!empty($orderBy)) {
			$orderBy = ' ORDER BY ' . $orderBy;
		}
		
		/* LIMIT */
		if (!empty($limit)) {
			$limit = ' LIMIT ' . $limit;
		}
		
		/* build the sql string */
		$where = '';
		if (count($conditions)) {
			$where = ' WHERE ' . implode(' AND ', $conditions);
		}
		$sql = 'SELECT * FROM ' . implode(', ', array_keys($tables)) 
		     . $where . $orderBy . $limit;
		     
		return $sql;
	}
	
	/**
	 * Builds an SQL WHERE clause from an array.
	 * Keys are considered as fields name. If a value
	 * is an array, the first item will be used and it
	 * won't be quoted
	 *
	 * @param array $where
	 * @param string $table OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 */
	function db_build_where($where, $table = null, $escape = true)
	{
		/* adds a dot at the end of $table */
		if ($table !== null) {
			$table = $table . '.';
		}
		
		/* creates the sql condition for each key/value pair */
		$conditions = array();
		foreach ($where as $field => $value) {
		
			/* escapes the value */
			if (!is_array($value) && $escape) {
				$value = call_user_func(config_get('database_func_escape'), $value);
			}
			
			/* quote the value */
			if (!is_array($value)) {
				$value = "'$value'";
			} else {
				$value = $value[0];
			}
			
			$conditions[] = "${table}${field}=$value";
		}
		
		return implode(' AND ', $conditions);
	}

	/**
	 * Transforms raw results to an array of row
	 *
	 * @param mixed $results
	 * @param bool $unique OPTIONAL Only one row (default false)
	 * @return array|bool Returns false only if $unique is true and no results were returned
	 */
	function db_fetch_results($results, $unique = false)
	{
		$func_fetch = config_get('database_func_fetch');
		
		/* fetch rows */
		$rows = array();
		while ($row = call_user_func($func_fetch, $results)) {
			$rows[] = $row;
			if($unique) break;
		}

		/* only retreives one row, no need to wrap it into an array */
		if($unique) {
			if (count($rows) == 0) {
				return false;
			}
			$rows = $rows[0];
		}
		
		return $rows;
	}

