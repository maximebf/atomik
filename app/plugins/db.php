<?php
/**
 * Atomik Framework
 * 
 * @package Atomik
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */
 
/* default configuration */
Atomik::setDefault(array(
    'db' => array (

    	/* enable/disable auto connection */
    	'autoconnect'	=> false,
    	
    	/* connection string (see PDO) */
    	'dsn' 			=> 'mysql:host=localhost;dbname=atomik',
    	
    	/* username */
    	'username'		=> 'root',
    	
    	/* password */
    	'password'		=> ''
    	
    )
));

/* automatic connection */
if (Atomik::get('db/autoconnect', false) === true) {
	Atomik::registerEvent('Atomik::Dispatch::Before', array('Db', 'connect'));
}

/**
 * Helpers function for handling databases
 *
 * @package Atomik
 * @subpackage Db
 */
class Db
{
	/**
	 * The pdo instance
	 *
	 * @var PDO
	 */
	public static $pdo;
	
	/**
	 * Connects to the database using the config values
	 *
	 * @return Db
	 */
	public static function connect()
	{
		/* connection information */
		$dsn = Atomik::get('db/dsn');
		$username = Atomik::get('db/username');
		$password = Atomik::get('db/password');
	
		/* creates the pdo instance */
		self::$pdo = new PDO($dsn, $username, $password);
		return self::$pdo;
	}
	
	/**
	 * Prepares and executes a statement
	 *
	 * @param string $query
	 * @param array $params OPTIONAL
	 * @return PDOStatement
	 */
	public static function query($query, $params = array())
	{
		$stmt = self::$pdo->prepare($query);
		$stmt->execute($params);
		return $stmt;
	}
	
	/**
	 * Executes a query without results
	 *
	 * @see PDO::exec()
	 * @param string $query
	 * @return int|bool
	 */
	public static function exec($query)
	{
		return self::$pdo->exec($query);
	}
	
	/**
	 * Prepare a statement
	 *
	 * @see PDO::prepare()
	 * @param string $query
	 * @param array $options OPTIONAL
	 * @return PDOStatement
	 */
	public static function prepare($query, $options = array())
	{
		return self::$pdo->prepare($query, $options);
	}
	
	/**
	 * Find the first row matching the arguments
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @return array
	 */
	public static function find($tables, $where = null, $orderBy = '', $limit = '')
	{
		$stmt = self::executeSelect($tables, $where, $orderBy, $limit);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row;
	}
	
	/**
	 * Finds all rows matching the arguments
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @return array
	 */
	public static function findAll($tables, $where = null, $orderBy = '', $limit = '')
	{
		return self::executeSelect($tables, $where, $orderBy, $limit);
	}
	
	/**
	 * Inserts a row inside the database.
	 * $data must be an array where keys are column name
	 * and their associated value the value to insert in the
	 * database
	 *
	 * @param string $table
	 * @param array $data
	 * @return bool
	 */
	public static function insert($table, $data)
	{
		$fields = array_keys($data);
		$values = array_values($data);

		/* builds the sql string */
		$sql = 'INSERT INTO ' . $table . '(' . implode(', ', $fields)
			   . ') VALUES(' . implode(', ', array_fill(0, count($values), '?')) . ')';
	
		/* creates and executes the statement */
		$stmt = self::$pdo->prepare($sql);
		return $stmt->execute($values);
	}
	
	/**
	 * Updates a row 
	 *
	 * @see Db::buildWhere()
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 * @return bool
	 */
	public static function update($table, $data, $where)
	{
		/* creates the sql where clause */
		list($tables, $where, $values) = self::buildWhere(array($table => $where));
	
		/* extract fields and values and quotes values */
		$fields = array();
		foreach ($data as $field => $value) {
			$fields[] = $field . '=?';
		}
		$fields = implode(', ', $fields);
		
		/* statement params */
		$params = array_merge(array_values($data), $values);

		/* builds the sql string */
		$sql = 'UPDATE ' . $table . ' SET ' . $fields . $where;
	
		/* creates and executes the statement */
		$stmt = self::$pdo->prepare($sql);
		return $stmt->execute($params);
	}
	
	/**
	 * Deletes rows
	 *
	 * @see Db::buildWhere()
	 * @param array|string $tables
	 * @param array $where OPTIONAL
	 * @return bool
	 */
	public static function delete($tables, $where = array())
	{
		/* creates the sql where clause */
		list($tables, $where, $values) = self::buildWhere($tables, $where);
		
		/* sql string */
		$sql = 'DELETE FROM ' . implode(', ', $tables) . $where;
		
		/* creates and executes the statement */
		$stmt = self::$pdo->prepare($sql);
		return $stmt->execute($values);
	}
	
	/**
	 * Buids and executes a SELECT query
	 *
	 * @see Db::buildWhere()
	 * @param string|array $tables
	 * @param array $where OPTIONAL
	 * @param string $orderBy OPTIONAL
	 * @param string $limit OPTIONAL
	 * @return PDOStatement
	 */
	public static function executeSelect($tables, $where = null, $orderBy = '', $limit = '')
	{
		/* creates the sql where clause */
		list($tables, $where, $values) = self::buildWhere($tables, $where);
	
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
		
		/* creates and executes the pdo statement */	 
		$stmt = self::$pdo->prepare($sql);
		$stmt->execute($values);
		
		return $stmt;
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
	 * @param string $operator OPTIONAL (default ' AND ')
	 * @return array
	 */
	public static function buildWhere($tables, $where = null, $operator = ' AND ')
	{
		$sql = '';
		
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
		$values = array();
		foreach ($tables as $table => $fields) {
			foreach ($fields as $field => $value) {
				/* escapes the value */	
				if (!is_array($value)) {
					$values[] = $value;
					$value = '?';
				}
				$conditions[] = "${table}.${field}=$value";
			}
		}
		if (count($conditions)) {
			$sql = ' WHERE ' . implode($operator, $conditions);
		}
		
		return array($tables, $sql, $values);
	}
}

