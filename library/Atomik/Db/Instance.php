<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2009 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Atomik
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/**
 * Helpers function for handling databases
 *
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Instance
{
	/**
	 * The pdo instance
	 *
	 * @var PDO
	 */
	public $pdo;
	
	/**
	 * Connection information for lazy loading
	 * 
	 * @var array
	 */
	public $connectionInfo;
	
	/**
	 * @var string
	 */
	protected $_tablePrefix;
	
	/**
	 * @var bool
	 */
	protected $_cacheEnabled = false;
	
	/**
	 * @var array
	 */
	protected $_queryCache = array();
	
	/**
	 * Constructor
	 *
	 * @param 	PDO 	$pdo
	 */
	public function __construct($dsnOrPdo = null, $username = null, $password = null)
	{
		if ($dsnOrPdo instanceof PDO) {
			$this->pdo = $pdo;
			return;
		}
		
		$this->connectionInfo = array(
			'dsn' 		=> $dsnOrPdo,
			'username' 	=> $username,
			'password' 	=> $password
		);
	}
	
	/**
	 * Connects to the database using the config values
	 */
	public function connect($dsn = null, $username = null, $password = null)
	{
		if ($this->pdo !== null) {
			// already connected
			return;
		}
		
		if ($dsn === null && $this->connectionInfo !== null) {
			$dsn = $this->connectionInfo['dsn'];
			$username = $this->connectionInfo['username'];
			$password = $this->connectionInfo['password'];
		}
		
		// creates the pdo instance
		try {
		    $this->pdo = new PDO($dsn, $username, $password);
			$this->connectionInfo = array(
				'dsn' 		=> $dsn,
				'username' 	=> $username,
				'password' 	=> $password
			);
		} catch (Exception $e) {
			require_once 'Atomik/Db/Exception.php';
			throw new Atomik_Db_Exception('Database connection failed');
		}
	}
	
	/**
	 * Closes the database connection
	 */
	public function disconnect()
	{
		$this->pdo = null;
		$this->_pdoDriver = null;
		$this->emptyCache();
	}
	
	/**
	 * Returns the PDO driver being used
	 * 
	 * @return string
	 */
	public function getPdoDriverName()
	{
		$this->connect();
		return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
	
	/**
	 * Sets the prefix that will be prepended to all table names
	 * 
	 * @param	string	$prefix
	 */
	public function setTablePrefix($prefix)
	{
		$this->_tablePrefix = $prefix;
	}
	
	/**
	 * Returns the table prefix
	 * 
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->_tablePrefix;
	}
	
	/**
	 * Sets whether to cache query results or not
	 * 
	 * @param bool $enable
	 */
	public function setCacheEnable($enable = true)
	{
		$this->_cacheEnabled = $enable;
	}
	
	/**
	 * Checks if the query cache is enabled.
	 * Query are cached only if constructed with Atomik_Db_Query
	 * 
	 * @return bool
	 */
	public function isCacheEnable()
	{
		return $this->_cacheEnabled;
	}
	
	/**
	 * Empties the query cache
	 * 
	 * @param Atomik_Db_Query $query If specified will clear the cache only for this query
	 */
	public function emptyCache(Atomik_Db_Query $query = null)
	{
		if ($query !== null) {
			$hash = $query->toHash();
			if (isset($this->_queryCache[$hash])) {
				unset($this->_queryCache[$hash]);
			}
			return;
		}
		$this->_cacheEnabled = array();
	}
	
	/**
	 * Returns error information
	 * 
	 * @return array
	 */
	public function getErrorInfo()
	{
		return $this->pdo->errorInfo();
	}
	
	/**
	 * Prepares and executes a statement
	 *
	 * @param 	string|Atomik_Db_Query	$query
	 * @param 	array 					$params
	 * @return 	PDOStatement
	 */
	public function query($query, $params = array())
	{
		$this->connect();
		if ($query instanceof Atomik_Db_Query) {
			return $this->_executeQuery($query);
		}
		
		$stmt = $this->pdo->prepare((string) $query);
		$stmt->execute($params);
		
		return $stmt;
	}
	
	/**
	 * Executes a query without results
	 *
	 * @see PDO::exec()
	 * @param 	string 		$query
	 * @return 	int|bool
	 */
	public function exec($query)
	{
		$this->connect();
		return $this->pdo->exec((string) $query);
	}
	
	/**
	 * Prepares a statement
	 *
	 * @see PDO::prepare()
	 * @param 	string 			$query
	 * @param 	array 			$options
	 * @return 	PDOStatement
	 */
	public function prepare($query, $options = array())
	{
		$this->connect();
		return $this->pdo->prepare((string) $query, $options);
	}
	
	/**
	 * Finds the first row matching the arguments
	 *
	 * @see Atomik_Db_Instance::findAll()
	 * @param 	string 			$table
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$offset
	 * @param 	string|array 	$fields
	 * @return 	mixed						False if nothing found
	 */
	public function find($table, $where = null, $orderBy = null, $offset = 0, $fields = null)
	{
		$limit = array($offset, 1);
		
		if (($result = self::findAll($table, $where, $orderBy, $limit, $fields)) === false) {
			return false;
		}
		
		$row = $result->fetch();
		$result->closeCursor();
		return $row;
	}
	
	/**
	 * Finds all rows matching the arguments
	 *
	 * @see Atomik_Db_Query
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$limit
	 * @param 	string|array 	$fields
	 * @return 	Atomik_Db_Query_Result
	 */
	public function findAll($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		$this->connect();
		$query = $this->_buildQuery($table, $where, $orderBy, $limit, $fields);
		return $this->_executeQuery($query);
	}
	
	/**
	 * Returns the value of the specified column of the first row to be found
	 * 
	 * @see Atomik_Db_Instance::find()
	 * @param 	string 			$table
	 * @param 	string 			$column
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$offset
	 * @return 	mixed						False if nothing found
	 */
	public function findValue($table, $column, $where = null, $orderBy = null, $offset = 0)
	{
		if (($row = self::find($table, $where, $orderBy, $offset, array($column))) === false) {
			return false;
		}
		
		if (!array_key_exists($column, $row)) {
			return false;
		}
		return $row[$column];
	}
	
	/**
	 * Perform a SELECT COUNT(*) query
	 *
	 * @see Atomik_Db_Instance::buildWhere()
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$limit
	 * @return 	int
	 */
	public function count($table, $where = null, $limit = null)
	{
		$this->connect();
		
		$query = $this->_buildQuery($table, $where, null, $limit, 'COUNT(*)');
		if (($result = $this->_executeQuery($query)) === false) {
			return 0;
		}
		
		$count = $result->getStatement()->fetchColumn();
		$result->closeCursor();
		return $count;
	}
	
	/**
	 * Checks if some rows exist with the specified $where
	 * Kinf of an alias of {@see Atomik_Db_Instance::count()}
	 * 
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$limit
	 * @return 	bool
	 */
	public function has($table, $where, $limit = null)
	{
		return $this->count($table, $where, $limit) > 0;
	}
	
	/**
	 * Inserts a row inside the database.
	 * $data must be an array where keys are column name
	 * and their associated value the value to insert in the
	 * database
	 *
	 * @param 	string 		$table
	 * @param 	array 		$data
	 * @return 	bool|int 			Last insert id or false
	 */
	public function insert($table, $data)
	{
		$this->connect();
		
		$query = new Atomik_Db_Query();
		$query->setTablePrefix($this->_tablePrefix)->insertInto($table)->values($data);
	
		if ($query->execute($this->pdo) === false) {
			return false;
		}
		return $this->pdo->lastInsertId();
	}
	
	/**
	 * Updates a row 
	 *
	 * @see Atomik_Db_Instance::buildWhere()
	 * @param 	string 	$table
	 * @param 	array 	$data
	 * @param 	array 	$where
	 * @return 	bool
	 */
	public function update($table, $data, $where)
	{
		$this->connect();
		
		$query = new Atomik_Db_Query();
		$query->setTablePrefix($this->_tablePrefix)->update($table)->set($data)->where($where);
		return $query->execute($this->pdo) !== false;
	}
	
	/**
	 * Inserts or updates values depending if they're already in the database.
	 * 
	 * Uses {@see Atomik_Db_Instance::has()} to check if data is already inserted.
	 * If $where is null, $data will be used as the where clause. $where can also
	 * be a string representing a key of the data array
	 * 
	 * @param 	string 			$table
	 * @param 	array 			$data
	 * @param 	array|string 	$where
	 * @return 	int|bool				Last insert id if it's an insert, true for success on update, false otherwise
	 */
	public function set($table, $data, $where = null)
	{
		if ($where === null) {
			$where = $data;
		} else if (is_string($where) && array_key_exists($where, $data)) {
			$where = array($where => $data[$where]);
		} else if (is_array($where)) {
			$tmpWhere = $where;
			$where = array();
			foreach ($tmpWhere as $key => $value) {
				if (is_int($key)) {
					if (array_key_exists($value, $data)) {
						$where[$value] = $data[$value];
					}
				} else {
					$where[$key] = $value;
				}
			}
		} else {
			return false;
		}
		
		if ($this->has($table, $where)) {
			return $this->update($table, $data, $where);
		}
		return $this->insert($table, $data);
	}
	
	/**
	 * Deletes rows
	 *
	 * @see Atomik_Db_Instance::buildWhere()
	 * @param 	array|string 	$table
	 * @param 	array 			$where
	 * @return 	bool
	 */
	public function delete($table, $where = array())
	{
		$this->connect();
		$query = new Atomik_Db_Query();
		$query->setTablePrefix($this->_tablePrefix)->delete()->from($table)->where($where);
		return $query->execute($this->pdo) !== false;
	}
	
	/**
	 * Executes a query.
	 * Uses the cache version if available
	 * 
	 * @param	Atomik_Db_Query		$query
	 * @return 	Atomik_Db_Query_Result
	 */
	protected function _executeQuery(Atomik_Db_Query $query)
	{
		$hash = $query->toHash();
		if ($this->_cacheEnabled && isset($this->_queryCache[$hash])) {
			$this->_queryCache[$hash]->rewind();
			return $this->_queryCache[$hash];
		}
		
		if (($result = $query->execute($this->pdo)) === false) {
			return false;
		}
		
		if ($this->_cacheEnabled) {
			$this->_queryCache[$hash] = $result;
			return $this->_queryCache[$hash];
		}
		return $result;
	}
	
	/**
	 * Builds a Atomik_Db_Query object
	 * 
	 * @see Atomik_Db_Query
	 * @param 	string|array 	$table
	 * @param 	array 			$where
	 * @param 	string 			$orderBy
	 * @param 	string 			$limit
	 * @param 	string|array 	$fields
	 * @return Atomik_Db_Query
	 */
	protected function _buildQuery($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		if (empty($fields)) {
			$fields = '*';
		}
		
		$query = new Atomik_Db_Query();
		$query->setTablePrefix($this->_tablePrefix);
		$query->select($fields)->from($table);
	
		if ($where !== null) {
			$query->where($where);
		}
		if ($orderBy !== null) {
			$query->orderBy($orderBy);
		}
		if ($limit !== null) {
			$query->limit($limit);
		}
		
		return $query;
	}
}

