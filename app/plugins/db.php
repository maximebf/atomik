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
		
		/* functions */
		'database_func_connect' 	=> 'mysql_connect',
		'database_func_close' 		=> 'mysql_close',
		'database_func_selectdb' 	=> 'mysql_select_db',
		'database_func_query' 		=> 'mysql_query',
		'database_func_fetch' 		=> 'mysql_fetch_array',
		'database_func_escape' 		=> 'mysql_real_escape_string'
	));

	/**
	 * Connects to the database
	 *
	 * @param array $args OPTIONAL Arguments passed to the database_func_connect function
	 * @return mixed Value returned by the database_connect function
	 */
	function db_connect($args = array())
	{
		global $_ATOMIK;
		
		if (count($args) === 0) {
			/* retreives database_func_connect args from the config */
			$args = config_get('database_args', array());
		}
	
		/* calls the connection functions */
		if (!($db = call_user_func_array(config_get('database_func_connect'), $args))) {
			trigger_error('Database connection failed');
		}
		
		/* stores the result into the registry */
		$_ATOMIK['db'] = $db;
		
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
		global $_ATOMIK;
		return call_user_func(config_get('database_func_close'), $_ATOMIK['db']);
	}

	/**
	 * Query the database
	 * 
	 * @param string $query
	 * @param array $params OPTIONAL Params for PDOStatement::execute()
	 * @return bool|resource|PDOStatement
	 */
	function db_query($query, $params = array())
	{
		global $_ATOMIK;
		return call_user_func(config_get('database_func_query'), $query, $_ATOMIK['db']);
	}

	/**
	 * Returns all rows matching the query
	 *
	 * @param string $query
	 * @param bool $unique OPTIONAL Only one row (default false)
	 * @return array|bool False if no row where found
	 */
	function db_select($query, $unique = false)
	{
		if ($results = db_query($query)) {
			return db_fetch_results($results, $unique);
		}
		
		return false;
	}
	
	/**
	 * Find the first row matching the arguments
	 *
	 * @see db_build_select_sql()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return array
	 */
	function db_find($tables, $where = null, $orderBy = '', $limit = '', $escape = true)
	{
		$sql = db_build_select_sql($tables, $where, $orderBy, $limit, $escape);
		return db_select_sql($sql, true);
	}
	
	/**
	 * Finds all rows matching the arguments
	 *
	 * @see db_build_select_sql()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return array
	 */
	function db_find_all($tables, $where = array(), $orderBy = '', $limit = '', $escape = true)
	{
		$sql = db_build_select_sql($tables, $where, $orderBy, $limit, $escape);
		return db_select_sql($sql);
	}

	/**
	 * Inserts data into the database
	 * The data array keys are fields name and their
	 * associated values the data. 
	 *
	 * @see db_build_where()
	 * @param string $table
	 * @param array $data
	 * @return mixed
	 */
	function db_insert($table, $data)
	{
		/* extract fields and values and quotes values */
		$fields = array_keys($data);
		$values = array_map('db_quote', array_values($data));

		/* builds the sql string */
		$query = 'INSERT INTO ' . $table . '(' . implode(', ', $fields) 
		       . ') VALUES(' . implode(', ', $values) . ')';
		
		/* executes the query */
		return db_query($query);
	}

	/**
	 * Updates a row 
	 *
	 * @see db_build_where()
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 * @return mixed
	 */
	function db_update($table, $data, $where)
	{
		/* creates the where clause */
		list($tables, $where) = db_build_where(array($table => $where));
		
		/* extract fields and values and quotes values */
		$fields = array();
		foreach ($data as $field => $value) {
			$fields[] = $field . '=' . db_quote($value);
		}
		$fields = implode(', ', $fields);

		/* builds the sql string */
		$query = 'UPDATE ' . $table . ' SET ' . $fields . $where;
		
		/* executes the query */
		return db_query($query);
	}
	
	/**
	 * Deletes rows
	 *
	 * @see db_build_where()
	 * @param string $tables
	 * @param array $where OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return mixed
	 */
	function db_delete($tables, $where = array(), $escape = true)
	{
		/* creates the where clause */
		list($tables, $where) = db_build_where($tables, $where, $escape);
		
		/* sql string */
		$sql = 'DELETE FROM ' . implode(', ', $tables) . $where;
		
		/* executes */
		return db_query($sql);
	}
	
	/**
	 * Buils an sql SELECT query using the $tables and $where arguments
	 *
	 * @see db_build_where()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return string
	 */
	function db_build_select_sql($tables, $where = null, $orderBy = '', $limit = '', $escape = true)
	{
		/* creates the where clause */
		list($tables, $where) = db_build_where($tables, $where, $escape);
		
		/* ORDER BY */
		if (!empty($orderBy)) {
			$orderBy = ' ORDER BY ' . $orderBy;
		}
		
		/* LIMIT */
		if (!empty($limit)) {
			$limit = ' LIMIT ' . $limit;
		}
		
		/* build the sql string */
		$sql = 'SELECT * FROM ' . implode(', ', array_keys($tables)) 
		     . $where . $orderBy . $limit;
		     
		return $sql;
	}
	
	/**
	 * Builds an sql where clause
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
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param bool $escape OPTIONAL (default true)
	 * @return array
	 */
	function db_build_where($tables, $where = null, $escape = true)
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
			foreach ($fields as $field => $value) {
				$value = db_quote($value, $escape);
				$conditions[] = "$table.$field=$value";
			}
		}
		
		/* returns the where clause if there was conditions */
		if (count($conditions)) {
			return array($tables, ' WHERE ' . implode(' AND ', $conditions));
		}
		
		return array($tables, '');
	}

	/**
	 * Transforms raw results to an array of row
	 *
	 * @param resource|PDOStatement $results
	 * @param bool $unique OPTIONAL Only one row (default false)
	 * @return array|bool Returns false only if $unique is true and no results were returned
	 */
	function db_fetch_results($results, $unique = false)
	{
		/* fetch rows */
		$rows = array();
		$func_fetch = config_get('database_func_fetch');
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
	
	/**
	 * Escapes and quotes a value (unless it's an array, which in this
	 * case returns the first item)
	 *
	 * @param string|array $value
	 * @param bool $escape OPTIONAL (default true)
	 * @return string
	 */
	function db_quote($value, $escape = true)
	{
		/* escapes the value */
		if (!is_array($value) && $escape) {
			$value = db_escape($value);
		}
	
		/* quote the value */
		if (!is_array($value)) {
			$value = "'$value'";
		} else {
			$value = $value[0];
		}
		
		return $value;
	}
	
	/**
	 * Escapes a string for an sql query
	 *
	 * @param string $value
	 * @return string
	 */
	function db_escape($value)
	{
		call_user_func(config_get('database_func_escape'), $value);
	}

